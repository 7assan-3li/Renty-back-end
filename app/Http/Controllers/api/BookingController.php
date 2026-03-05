<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Carbon\Carbon; // ✅ تم الإضافة هنا لاستخدام دوال الوقت والتاريخ

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function index(Request $request)
    {
        $bookings = \App\Models\Booking::with(['car', 'payment'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($bookings);
    }

    public function store(StoreBookingRequest $request)
    {
        try {
            $result = $this->bookingService->createBooking($request->user()->id, $request->validated());

            return response()->json([
                'message' => 'Booking initiated successfully',
                'booking_id' => $result['booking']->id,
                'user_id' => $request->user()->id,
                'client_secret' => $result['paymentIntent']->client_secret,
                'amount' => $result['paymentIntent']->amount / 100,
                'currency' => $result['paymentIntent']->currency,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function rate(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
        ]);

        $booking = \App\Models\Booking::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($booking->rating) {
            return response()->json(['message' => 'Booking already rated'], 400);
        }

        $this->bookingService->rateBooking($booking, $request->rating);

        return response()->json(['message' => 'Rating submitted successfully']);
    }

    public function confirm(Request $request, $id)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
        ]);

        $booking = \App\Models\Booking::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($booking->payment_status === 'paid') {
            return response()->json(['message' => 'Booking already paid'], 200);
        }

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $paymentIntent = \Stripe\PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status !== 'succeeded') {
                return response()->json(['message' => 'Payment not successful', 'status' => $paymentIntent->status], 400);
            }

            if (!isset($paymentIntent->metadata->booking_id) || $paymentIntent->metadata->booking_id != $booking->id) {
                return response()->json(['message' => 'Invalid payment intent for this booking'], 400);
            }

            $this->bookingService->confirmPayment($booking, $paymentIntent->id);

            return response()->json(['message' => 'Payment confirmed successfully']);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // =====================================================================
    // 🔔 دالة توليد الإشعارات المستنبطة (Inferred Notifications)
    // =====================================================================
    public function getNotifications(Request $request)
    {
        // جلب كل حجوزات المستخدم مع تفاصيل السيارة
        $bookings = \App\Models\Booking::with('car')->where('user_id', $request->user()->id)->get();
        
        $notifications = collect();
        $now = Carbon::now();

        foreach ($bookings as $booking) {
            $carName = $booking->car ? $booking->car->name : 'Car';
            $startDate = Carbon::parse($booking->start_date);
            $endDate = Carbon::parse($booking->end_date);

            // 1. إشعار: الدفع معلق (Pending Payment)
            if ($booking->payment_status === 'unpaid') {
                $notifications->push([
                    'type' => 'pending',
                    'title' => 'Pending Payment',
                    'message' => "Your booking for {$carName} is pending. Please complete the payment.",
                    'time' => $booking->created_at->diffForHumans(), // تولد صيغة مثل "2 hours ago"
                    'sort_time' => $booking->created_at
                ]);
            }

            // 2. إشعار: تأكيد الحجز (Booking confirmation)
            if ($booking->payment_status === 'paid' && $booking->status === 'done') {
                $notifications->push([
                    'type' => 'confirmation',
                    'title' => 'Booking confirmation',
                    'message' => "Your booking for {$carName} has been confirmed from {$startDate->format('M d')} to {$endDate->format('M d')}.",
                    'time' => $booking->updated_at->diffForHumans(),
                    'sort_time' => $booking->updated_at
                ]);
            }

            // 3. إشعار: تذكير (Reminder) - إذا كان الاستلام خلال 24 ساعة
            if ($booking->payment_status === 'paid' && $booking->status === 'done' && $startDate->isFuture() && $startDate->diffInHours($now) <= 24) {
                $notifications->push([
                    'type' => 'reminder',
                    'title' => 'Reminder',
                    'message' => "Your pickup time for {$carName} is approaching.",
                    'time' => 'Just now',
                    'sort_time' => $now
                ]);
            }

            // 4. إشعار: انتهاء المدة (Expired)
            if ($booking->payment_status === 'paid' && $booking->status === 'done' && $endDate->isPast()) {
                $notifications->push([
                    'type' => 'expired',
                    'title' => 'The reservation period has expired',
                    'message' => "Your booking for {$carName} has ended. Please return or extend your booking.",
                    'time' => $endDate->diffForHumans(),
                    'sort_time' => $endDate
                ]);
            }
        }

        // ترتيب الإشعارات من الأحدث للأقدم وإرسالها
        $sortedNotifications = $notifications->sortByDesc('sort_time')->values()->all();

        return response()->json($sortedNotifications);
    }
}