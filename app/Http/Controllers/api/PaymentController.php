<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment; // Assuming Payment model exists or will be created
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

        // Ensure booking is not already paid
        if ($booking->payment_status === 'paid') {
            return response()->json(['message' => 'Booking is already paid'], 400);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $booking->total_price * 100, // Amount in cents
                'currency' => 'usd',
                'metadata' => [
                    'booking_id' => $booking->id,
                    'user_id' => $request->user()->id,
                ],
            ]);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'booking_id' => 'required|exists:bookings,id',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status === 'succeeded') {
                // Update Booking Status
                $booking = Booking::findOrFail($request->booking_id);
                $booking->payment_status = 'paid';
                $booking->save();

                // Record Payment (Optional but recommended)
                // Payment::create([...]); 

                return response()->json(['message' => 'Payment confirmed successfully']);
            } else {
                return response()->json(['message' => 'Payment not successful'], 400);
            }

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
