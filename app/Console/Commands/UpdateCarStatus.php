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
        $this->info('Updating car statuses and sending notifications...');

        $now = Carbon::now();
        $today = $now->format('Y-m-d');

        // 1. Find cars that should be BOOKED
        // We check for both 'confirmed' and 'done' statuses to be safe
        $activeBookings = Booking::whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::DONE])
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->get();

        $bookedCarIds = $activeBookings->pluck('car_id')->unique();

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

        // 3. Send Reminders (Starting tomorrow)
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $reminderBookings = Booking::with(['user', 'car'])
            ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::DONE])
            ->whereDate('start_date', $tomorrow)
            ->get();

        foreach ($reminderBookings as $booking) {
            // Send reminder only once (we check if a notification was sent in the last 24h for this user/type)
            // For simplicity in this project, we'll send it if not already sent for this specific booking
            $exists = \Illuminate\Support\Facades\DB::table('notifications')
                ->where('notifiable_id', $booking->user_id)
                ->where('data', 'like', '%"type":"reminder"%')
                ->where('data', 'like', '%"booking_id":' . $booking->id . '%') // We should add booking_id to data
                ->exists();

            if (!$exists) {
                $booking->user->notify(new \App\Notifications\BookingNotification(
                    'Reminder',
                    "Your pickup time for {$booking->car->name} is approaching (Tomorrow).",
                    'reminder',
                    $booking->id
                ));
            }
        }

        // 4. Send Expired Notifications (Ended yesterday or ending today)
        $expiredBookings = Booking::with(['user', 'car'])
            ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::DONE])
            ->whereDate('end_date', '<', $today)
            ->get();

        foreach ($expiredBookings as $booking) {
            $exists = \Illuminate\Support\Facades\DB::table('notifications')
                ->where('notifiable_id', $booking->user_id)
                ->where('data', 'like', '%"type":"expired"%')
                ->where('data', 'like', '%"booking_id":' . $booking->id . '%')
                ->exists();

            if (!$exists) {
                $booking->user->notify(new \App\Notifications\BookingNotification(
                    'The reservation period has expired',
                    "Your booking for {$booking->car->name} has ended. Please return or extend your booking.",
                    'expired',
                    $booking->id
                ));
            }
        }

        $this->info('Car status and notification update completed.');
    }
}
