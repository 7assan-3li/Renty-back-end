<?php

namespace App\Console\Commands;

use App\Models\Car;
use App\Models\User;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class TestRating extends Command
{
    protected $signature = 'test:rating';
    protected $description = 'Test the rating process';

    public function handle()
    {
        $this->info("Starting Rating Test...");

        // Ensure we have a user
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create();
        }

        // Ensure we have a car
        $car = Car::first();
        if (!$car) {
            $this->info("No cars found. Creating one...");
            $category = \App\Models\Category::first();
            if (!$category) {
                $category = \App\Models\Category::create(['name' => 'Test Category', 'image' => 'test.jpg']);
            }
            $car = Car::create([
                'name' => 'Test Car',
                'image' => 'test_car.jpg',
                'description' => 'A test car',
                'model' => '2024',
                'latitude' => 0,
                'longitude' => 0,
                'price_per_day' => 50,
                'category_id' => $category->id,
                'status' => 'available'
            ]);
        }

        // Reset Car Rating
        $car->update(['rating' => 0, 'rating_count' => 0]);
        $this->info("Reset Car Rating to 0.");

        $service = new BookingService();

        // Create a booking to rate
        $data = [
            'car_id' => $car->id,
            'start_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(12)->format('Y-m-d'),
        ];

        $result = $service->createBooking($user->id, $data);
        $booking = $result['booking'];
        $this->info("Created Booking ID: {$booking->id}");

        // Rate the booking
        $rating = 5;
        $this->info("Rating Booking with {$rating} stars...");

        $service->rateBooking($booking, $rating);

        // Reload car to check rating
        $car->refresh();
        $this->info("Car Rating: {$car->rating} | Count: {$car->rating_count}");

        if ($car->rating == 5 && $car->rating_count == 1) {
            $this->info("✅ First Rating Verified!");
        } else {
            $this->error("❌ First Rating Failed!");
        }

        // Rate another booking
        $result2 = $service->createBooking($user->id, $data);
        $booking2 = $result2['booking'];
        $this->info("Created Second Booking ID: {$booking2->id}");

        $rating2 = 3;
        $this->info("Rating Second Booking with {$rating2} stars...");
        $service->rateBooking($booking2, $rating2);

        $car->refresh();
        $expectedRating = (5 + 3) / 2;
        $this->info("Car Rating: {$car->rating} | Count: {$car->rating_count}");

        if ($car->rating == $expectedRating && $car->rating_count == 2) {
            $this->info("✅ Average Rating Verified!");
        } else {
            $this->error("❌ Average Rating Failed! Expected {$expectedRating}");
        }
    }
}
