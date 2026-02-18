<?php

namespace App\Services;

use App\Models\Payment;

class FinanceService
{
    /**
     * Get financial statistics.
     */
    public function getStats()
    {
        return [
            'totalRevenue' => Payment::sum('amount'),
            'todayRevenue' => Payment::whereDate('created_at', today())->sum('amount'),
            'monthRevenue' => Payment::whereMonth('created_at', now()->month)->sum('amount'),
        ];
    }

    /**
     * Get recent payments with pagination.
     */
    public function getRecentPayments($limit = 15)
    {
        return Payment::with(['booking.user', 'booking.car'])
            ->latest()
            ->paginate($limit);
    }
}
