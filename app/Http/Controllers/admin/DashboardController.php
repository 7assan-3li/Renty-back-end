<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $stats = $this->dashboardService->getStats();
        $monthlyRevenue = $this->dashboardService->getMonthlyRevenue();
        $topCars = $this->dashboardService->getTopCars();
        $recentBookings = $this->dashboardService->getRecentBookings();

        // Format chart data
        $chartLabels = $monthlyRevenue->keys();
        $chartData = $monthlyRevenue->values();

        return view('admin.dashboard', compact('stats', 'topCars', 'recentBookings', 'chartLabels', 'chartData'));
    }
}
