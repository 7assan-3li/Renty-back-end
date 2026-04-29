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
        $bookings = \App\Models\Booking::with(['car.category', 'payment'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($bookings);
    }

    public function show(Request $request, $id)
    {
        $booking = \App\Models\Booking::with(['car.category', 'payment'])
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // إضافة عدد الأيام يدوياً في الرد
        $startDate = \Carbon\Carbon::parse($booking->start_date);
        $endDate = \Carbon\Carbon::parse($booking->end_date);
        $booking->rental_days = $startDate->diffInDays($endDate) + 1;

        return response()->json($booking);
    }

    public function destroy(Request $request, $id)
    {
        $booking = \App\Models\Booking::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->where('payment_status', 'unpaid') // السماح بحذف غير المدفوع فقط
            ->firstOrFail();

        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully']);
    }

    public function store(StoreBookingRequest $request)
    {
        try {
            $result = $this->bookingService->createBooking($request->user()->id, $request->validated());

            $response = [
                'status' => true,
                'message' => 'Booking initiated successfully',
                'booking_id' => $result['booking']->id,
                'user_id' => $request->user()->id,
                'payment_method' => $result['payment_method'],
            ];

            // إذا كان الدفع عبر Stripe، نضيف الـ client_secret
            if ($result['payment_method'] === 'Stripe') {
                $response['client_secret'] = $result['paymentIntent']->client_secret;
                $response['amount'] = $result['paymentIntent']->amount / 100;
                $response['currency'] = $result['paymentIntent']->currency;
            }

            return response()->json($response, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => false], 400);
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
}