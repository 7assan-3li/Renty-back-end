<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        if ($booking->payment_status === 'paid') {
            return response()->json(['message' => 'Booking is already paid'], 400);
        }

        // استخدام config بدلاً من env لضمان استقرار المفاتيح
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // ✅ الحل النهائي والوحيد لهذا الخطأ:
            // 1. نأخذ السعر الإجمالي (مثلاً 1950.00).
            // 2. نضربه في 100 لتحويله لسنتات.
            // 3. نستخدم round لتقريب أي فواصل ناتجة عن حسابات الضرائب أو التأمين.
            // 4. نحوله قسرياً إلى (int) للتأكد من اختفاء الفاصلة تماماً.
            $amountInCents = (int) round($booking->total_price * 100);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents, 
                'currency' => 'usd',
                'metadata' => [
                    'booking_id' => $booking->id,
                    'user_id' => $request->user()->id,
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'booking_id' => $booking->id,
            ]);

        } catch (\Exception $e) {
            // هنا سيظهر الخطأ إذا فشل Stripe في قبول الرقم
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'booking_id' => 'required|exists:bookings,id',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status === 'succeeded') {
                $booking = Booking::findOrFail($request->booking_id);
                
                // تحديث الحالات كما طلبت لمشروعك
                $booking->update([
                    'payment_status' => 'paid',
                    'status' => 'done'
                ]);

                return response()->json(['message' => 'Payment confirmed successfully']);
            } 
            
            return response()->json(['message' => 'Payment not successful'], 400);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
};