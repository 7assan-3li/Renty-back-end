<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\FinanceService;

class FinanceController extends Controller
{
    protected $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function index()
    {
        $stats = $this->financeService->getStats();
        $recentPayments = $this->financeService->getRecentPayments();

        return view('admin.finance.index', array_merge($stats, ['recentPayments' => $recentPayments]));
    }
}
