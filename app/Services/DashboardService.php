<?php

namespace App\Services;

use App\Models\Car;
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    public function getStats()
    {
        return [
            'totalCars' => Car::count(),
            'availableCars' => Car::where('status', \App\Constants\CarStatus::AVAILABLE)->count(),
            'bookedCars' => Car::where('status', \App\Constants\CarStatus::BOOKED)->count(),
            'totalUsers' => User::where('role', 'user')->count(), // Assuming 'user' role
            'totalBookings' => Booking::count(),
            'pendingBookings' => Booking::where('status', \App\Constants\BookingStatus::PENDING)->count(),
            'totalRevenue' => Payment::sum('amount'),
        ];
    }

    public function getMonthlyRevenue()
    {
        // Get revenue for the last 6 months
        $revenueData = Payment::select(
            DB::raw('sum(amount) as sum'),
            DB::raw("DATE_FORMAT(created_at,'%M') as month")
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('created_at') // This might need better ordering logic
            ->pluck('sum', 'month');

        return $revenueData;
    }

    public function getTopCars()
    {
        return Car::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();
    }

    public function getRecentBookings()
    {
        return Booking::with(['user', 'car'])
            ->latest()
            ->take(5)
            ->get();
    }
}
