<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Car;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Notifications\BookingNotification;

class BookingService
{
    public function createBooking($userId, array $data)
    {
        $car = Car::findOrFail($data['car_id']);
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        
        // حساب عدد الأيام
        $days = $startDate->diffInDays($endDate) + 1; 
        
        // حساب السعر الإجمالي
        $totalPrice = $car->price_per_day * $days;

        // التحقق من توفر السيارة
        // السيارة تعتبر غير متاحة فقط وفقط إذا كان هناك حجز مدفوع بنجاح (Paid)
        $isAvailable = !Booking::where('car_id', $car->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<', $startDate)
                            ->where('end_date', '>', $endDate);
                    });
            })
            ->where('payment_status', 'paid') // لا تمنع الحجز إلا إذا تم الدفع فعلياً
            ->exists();

        if (!$isAvailable) {
            throw new \Exception('Car is already booked and paid for these dates.');
        }

        return DB::transaction(function () use ($userId, $data, $totalPrice, $car) {
            // 1. إنشاء سجل الحجز
            $booking = Booking::create([
                'user_id' => $userId,
                'car_id' => $car->id,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            // 2. إعداد Stripe
            Stripe::setApiKey(config('services.stripe.secret'));

            // ✅ حل مشكلة الخطأ FATAL ERROR:
            // نستخدم round لتقريب أي كسور ناتجة عن الحسابات، ثم نحولها لـ (int) صريح.
            $amountInCents = (int) round($totalPrice * 100);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents, // Stripe سيستلم الآن رقماً صحيحاً 100%
                'currency' => 'usd',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'booking_id' => $booking->id,
                    'user_id' => $userId,
                ],
            ]);

            // 3. إنشاء سجل الدفع الأولي
            Payment::create([
                'user_id' => $userId,
                'booking_id' => $booking->id,
                'transaction_id' => $paymentIntent->id,
                'amount' => $totalPrice,
                'currency' => 'usd',
                'status' => 'pending',
            ]);

            // 4. إرسال إشعار الدفع المعلق
            $booking->user->notify(new BookingNotification(
                'Pending Payment',
                "Your booking for {$car->name} is pending. Please complete the payment.",
                'pending',
                $booking->id
            ));

            return [
                'booking' => $booking,
                'paymentIntent' => $paymentIntent,
            ];
        });
    }

    public function confirmPayment(Booking $booking, $transactionId)
    {
        return DB::transaction(function () use ($booking, $transactionId) {
            // تحديث سجل الدفع
            $booking->payment()->update([
                'status' => 'succeeded',
                'transaction_id' => $transactionId
            ]);

            // ✅ تحديث حالة الحجز إلى "done" و "paid" كما طلبت
            $booking->update([
                'payment_status' => 'paid',
                'status' => 'done' 
            ]);

            // تحديث حالة السيارة إلى "محجوزة" إذا بدأ الإيجار اليوم
            if (Carbon::parse($booking->start_date)->isToday() || Carbon::parse($booking->start_date)->isPast()) {
                $booking->car->update(['status' => 'booked']);
            }

            // حذف إشعارات "الدفع المعلق" القديمة لهذا الحجز لكي لا تظل تظهر كـ "غير مدفوع"
            $booking->user->notifications()
                ->where('data->booking_id', $booking->id)
                ->where('data->type', 'pending')
                ->delete();

            // إرسال إشعار تأكيد الحجز الجديد
            $carName = $booking->car ? $booking->car->name : 'Car';
            $startDate = Carbon::parse($booking->start_date);
            $endDate = Carbon::parse($booking->end_date);
            
            $booking->user->notify(new BookingNotification(
                'Booking confirmation',
                "Your booking for {$carName} has been confirmed from {$startDate->format('M d')} to {$endDate->format('M d')}.",
                'confirmation',
                $booking->id
            ));

            return $booking;
        });
    }

    public function rateBooking(Booking $booking, $rating)
    {
        return DB::transaction(function () use ($booking, $rating) {
            $booking->update(['rating' => $rating]);

            $car = $booking->car;
            $newCount = $car->rating_count + 1;
            $newRating = (($car->rating * $car->rating_count) + $rating) / $newCount;

            $car->update([
                'rating' => $newRating,
                'rating_count' => $newCount,
            ]);

            return $booking;
        });
    }
}