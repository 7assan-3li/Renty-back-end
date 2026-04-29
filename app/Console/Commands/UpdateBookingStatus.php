<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Models\Car;
use App\Constants\CarStatus;
use Carbon\Carbon;

class UpdateBookingStatus extends Command
{
    protected $signature = 'bookings:update-status';
    protected $description = 'Update booking finished status and car availability based on dates';

    public function handle()
    {
        $today = Carbon::today();

        // 1. العثور على الحجوزات المنتهية (التي تاريخ انتهائها قبل اليوم ولم يتم تعليمها كمنتهية بعد)
        $expiredBookings = Booking::where('finished', 'No')
            ->where('end_date', '<', $today)
            ->where('payment_status', 'paid')
            ->get();

        foreach ($expiredBookings as $booking) {
            // تحديث حالة الحجز
            $booking->update(['finished' => 'Yes']);

            // تحديث حالة السيارة لتصبح متاحة مرة أخرى
            $booking->car->update(['status' => CarStatus::AVAILABLE]);
            
            $this->info("Booking #{$booking->id} marked as finished. Car #{$booking->car_id} is now Available.");
        }

        // 2. تحديث السيارات التي بدأ حجزها اليوم لتصبح Booked (احتياطاً)
        $activeBookings = Booking::where('finished', 'No')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->where('payment_status', 'paid')
            ->get();

        foreach ($activeBookings as $booking) {
            if ($booking->car->status !== CarStatus::BOOKED) {
                $booking->car->update(['status' => CarStatus::BOOKED]);
                $this->info("Car #{$booking->car_id} is now Booked for Booking #{$booking->id}.");
            }
        }

        $this->info('Booking statuses updated successfully.');
    }
}
