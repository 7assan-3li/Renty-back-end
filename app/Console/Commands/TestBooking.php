<?php

namespace App\Console\Commands;

use App\Models\Car;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class TestBooking extends Command
{
    protected $signature = 'test:booking';
    protected $description = 'Test the booking process';

    public function handle()
    {
        $this->info("Creating an Immediate Booking (Today)...");

        $user = User::first() ?? User::factory()->create();
        $this->info("User: {$user->email}");

        // Create a fresh car to avoid availability conflicts
        $category = \App\Models\Category::first() ?? \App\Models\Category::factory()->create();
        $car = Car::factory()->create([
            'category_id' => $category->id,
            'status' => \App\Constants\CarStatus::AVAILABLE
        ]);

        $this->info("Created Fresh Car: {$car->name} (ID: {$car->id})");

        $service = new BookingService();

        $startDate = Carbon::today()->format('Y-m-d');
        $endDate = Carbon::today()->addDays(2)->format('Y-m-d'); // Day after tomorrow

        $data = [
            'car_id' => $car->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        try {
            $this->info("Attempting to book for {$startDate} (Today) to {$endDate} (Day After Tomorrow)...");

            $result = $service->createBooking($user->id, $data);
            $booking = $result['booking'];

            $this->info("✅ Booking Created! ID: {$booking->id} | Status: {$booking->status}");

            // Simulate Payment to confirm it
            $this->info("Simulating Payment...");
            $service->confirmPayment($booking, 'txn_now_' . time());

            $this->info("Final Status: {$booking->status}");

            // Verify Car Status
            $booking->car->refresh();
            $this->info("Car Status: {$booking->car->status}");

            if ($booking->car->status === \App\Constants\CarStatus::BOOKED) {
                $this->info("✅ Verified: Car status updated to BOOKED immediately.");
            } else {
                $this->error("❌ Failed: Car status should be BOOKED.");
            }

            // --- Test Future Booking Logic ---
            $this->info("\n--- Step 3: Verifying Future/Scheduled Logic ---");

            // 1. Create a future booking (Tomorrow)
            $futureCar = Car::factory()->create([
                'category_id' => $category->id,
                'status' => \App\Constants\CarStatus::AVAILABLE
            ]);
            $this->info("Created Future Car: {$futureCar->name}");

            $futureBooking = $service->createBooking($user->id, [
                'car_id' => $futureCar->id,
                'start_date' => Carbon::tomorrow()->format('Y-m-d'),
                'end_date' => Carbon::tomorrow()->format('Y-m-d'),
            ])['booking'];

            // Confirm it
            $futureBooking->update(['status' => \App\Constants\BookingStatus::CONFIRMED]);

            // Run update command (Today) - Should NOT be booked yet
            $this->call('car:update-status');
            $futureCar->refresh();
            $this->info("Today (Before Start): Status is {$futureCar->status}");
            if ($futureCar->status !== \App\Constants\CarStatus::AVAILABLE) {
                $this->error("❌ Error: Car should be AVAILABLE today.");
            }

            // 2. Time Travel to Tomorrow
            $this->info("⏳ Traveling to Tomorrow...");
            Carbon::setTestNow(Carbon::tomorrow());

            // Run update command
            $this->call('car:update-status');
            $futureCar->refresh();
            $this->info("Tomorrow (During Booking): Status is {$futureCar->status}");
            if ($futureCar->status === \App\Constants\CarStatus::BOOKED) {
                $this->info("✅ Verified: Car became BOOKED automatically.");
            } else {
                $this->error("❌ Failed: Car should be BOOKED.");
            }

            // 3. Time Travel to Day After Tomorrow (Booking Ended)
            $this->info("⏳ Traveling to Day After Tomorrow...");
            Carbon::setTestNow(Carbon::tomorrow()->addDay());

            // Run update command
            $this->call('car:update-status');
            $futureCar->refresh();
            $this->info("Day After (Booking Ended): Status is {$futureCar->status}");
            if ($futureCar->status === \App\Constants\CarStatus::AVAILABLE) {
                $this->info("✅ Verified: Car became AVAILABLE automatically.");
            } else {
                $this->error("❌ Failed: Car should be AVAILABLE.");
            }

        } catch (\Exception $e) {
            $this->error("❌ Failed: " . $e->getMessage());
        }
    }
}
