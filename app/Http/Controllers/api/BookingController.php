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
}