<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Car;
use App\Services\BookingService;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class TestStripePayment extends Command
{
    protected $signature = 'test:stripe-payment';
    protected $description = 'Test the full Stripe payment flow';

    public function handle(BookingService $bookingService)
    {
        $this->info('Starting Stripe Payment Test...');

        // 1. Get or Create User
        $user = User::firstOrCreate(
            ['email' => 'test_stripe@example.com'],
            [
                'name' => 'Stripe Tester',
                'password' => bcrypt('password'),
                'role' => 'user',
                'phone' => '1234567890'
            ]
        );
        $this->info("User ID: {$user->id}");

        // 2. Create a new Car to ensure availability
        $car = Car::create([
            'name' => 'Stripe Test Car ' . time(),
            'brand' => 'TestBrand',
            'model' => '2025',
            'year' => 2025,
            'price_per_day' => 100,
            'status' => \App\Constants\CarStatus::AVAILABLE,
            'category_id' => 1, // Assuming category 1 exists
            'description' => 'Test Car',
            'image' => 'default.png',
            'latitude' => 24.7136,
            'longitude' => 46.6753
        ]);
        $this->info("Created Test Car ID: {$car->id}");

        // 3. Create Booking (Initiate)
        $this->info('Creating Booking...');
        $bookingData = [
            'car_id' => $car->id,
            'start_date' => Carbon::now()->addMonths(1)->toDateString(),
            'end_date' => Carbon::now()->addMonths(1)->addDays(2)->toDateString(),
        ];

        try {
            $result = $bookingService->createBooking($user->id, $bookingData);
            $booking = $result['booking'];
            $paymentIntent = $result['paymentIntent'];

            $this->info("Booking Created ID: {$booking->id}");
            $this->info("PaymentIntent Created ID: {$paymentIntent->id}");
            $this->info("Client Secret: {$paymentIntent->client_secret}");

            // 4. Confirm PaymentIntent with Stripe (Simulating Frontend)
            $this->info('Simulating Frontend Payment Confirmation with pm_card_visa...');
            Stripe::setApiKey(config('services.stripe.secret'));

            $confirmedIntent = $paymentIntent->confirm([
                'payment_method' => 'pm_card_visa', // Test card
                'return_url' => 'https://example.com/checkout/complete',
            ]);

            $this->info("PaymentIntent Status: {$confirmedIntent->status}");

            if ($confirmedIntent->status === 'succeeded') {
                $this->info('Payment Succeeded at Stripe.');

                // 5. Verify via BookingController logic (Simulating Backend Verification)
                $this->info('Verifying Payment via Backend Logic...');

                if (!isset($confirmedIntent->metadata->booking_id) || $confirmedIntent->metadata->booking_id != $booking->id) {
                    $this->error('Metadata mismatch!');
                } else {
                    $this->info('Metadata matched.');

                    // Call Service to confirm
                    $bookingService->confirmPayment($booking, $confirmedIntent->id);
                    $this->info('Booking Confirmed in Database.');

                    $booking->refresh();
                    $this->info("Final Booking Status: {$booking->status}");
                    $this->info("Final Payment Status: {$booking->payment_status}");
                    $this->info("Paid Transaction ID: {$booking->payment->transaction_id}");
                }

            } else {
                $this->warn("Payment Intent not succeeded immediately (Status: {$confirmedIntent->status}). Does it require action?");
            }

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
