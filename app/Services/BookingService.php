<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Car;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class BookingService
{
    public function createBooking($userId, array $data)
    {
        $car = Car::findOrFail($data['car_id']);
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $days = $startDate->diffInDays($endDate) + 1; // Include start day
        $totalPrice = $car->price_per_day * $days;

        // Check availability
        $isAvailable = !Booking::where('car_id', $car->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<', $startDate)
                            ->where('end_date', '>', $endDate);
                    });
            })
            ->exists();

        if (!$isAvailable) {
            throw new \Exception('Car is not available for the selected dates.');
        }

        return DB::transaction(function () use ($userId, $data, $totalPrice, $car) {
            // Create Booking
            $booking = Booking::create([
                'user_id' => $userId,
                'car_id' => $car->id,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'total_price' => $totalPrice,
                'status' => \App\Constants\BookingStatus::PENDING,
                'payment_status' => 'unpaid',
            ]);

            // Create Stripe Payment Intent
            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = PaymentIntent::create([
                'amount' => $totalPrice * 100, // Amount in cents
                'currency' => 'usd',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
                'metadata' => [
                    'booking_id' => $booking->id,
                    'user_id' => $userId,
                ],
            ]);

            // Create Payment Record
            Payment::create([
                'user_id' => $userId,
                'booking_id' => $booking->id,
                'transaction_id' => $paymentIntent->id,
                'amount' => $totalPrice,
                'currency' => 'usd',
                'status' => 'pending',
            ]);

            return [
                'booking' => $booking,
                'paymentIntent' => $paymentIntent,
            ];
        });
    }

    public function rateBooking(Booking $booking, $rating)
    {
        return DB::transaction(function () use ($booking, $rating) {
            // Update Booking Rating
            $booking->update(['rating' => $rating]);

            // Update Car Rating
            $car = $booking->car;

            // Calculate new average
            // New Rating = ((Old Rating * Old Count) + New Rating) / (Old Count + 1)
            $newCount = $car->rating_count + 1;
            $newRating = (($car->rating * $car->rating_count) + $rating) / $newCount;

            $car->update([
                'rating' => $newRating,
                'rating_count' => $newCount,
            ]);

            return $booking;
        });
    }

    public function confirmPayment(Booking $booking, $transactionId)
    {
        return DB::transaction(function () use ($booking, $transactionId) {
            // Update Payment Status
            $booking->payment()->update([
                'status' => 'succeeded',
                'transaction_id' => $transactionId
            ]);

            // Auto-confirm booking upon payment success
            $booking->update([
                'payment_status' => 'paid',
                'status' => \App\Constants\BookingStatus::CONFIRMED
            ]);

            // If booking starts today, update car status immediately
            if (\Carbon\Carbon::parse($booking->start_date)->isToday() || \Carbon\Carbon::parse($booking->start_date)->isPast()) {
                $booking->car->update(['status' => \App\Constants\CarStatus::BOOKED]);
            }

            return $booking;
        });
    }
}
