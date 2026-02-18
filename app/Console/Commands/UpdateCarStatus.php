<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Car;
use App\Models\Booking;
use App\Constants\CarStatus;
use App\Constants\BookingStatus;
use Carbon\Carbon;

class UpdateCarStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'car:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update car status based on current bookings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating car statuses...');

        $now = Carbon::now();
        $today = $now->format('Y-m-d');

        // 1. Find cars that should be BOOKED
        // i.e., have a confirmed booking that covers Today
        $bookedCarIds = Booking::where('status', BookingStatus::CONFIRMED)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->pluck('car_id')
            ->unique();

        // Update these cars to BOOKED
        $countBooked = Car::whereIn('id', $bookedCarIds)
            ->where('status', '!=', CarStatus::BOOKED)
            ->update(['status' => CarStatus::BOOKED]);

        $this->info("Updated $countBooked cars to BOOKED.");

        // 2. Find cars that should be AVAILABLE
        // i.e., currently BOOKED but NO active booking today
        $countAvailable = Car::where('status', CarStatus::BOOKED)
            ->whereNotIn('id', $bookedCarIds)
            ->update(['status' => CarStatus::AVAILABLE]);

        $this->info("Updated $countAvailable cars to AVAILABLE.");

        $this->info('Car status update completed.');
    }
}
