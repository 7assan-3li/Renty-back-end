@extends('layouts.admin')

@section('content')
    <section id="finance" class="content-section active">
        <div class="section-header">
            <h2 data-i18n="financeOverview">{{ __('financeOverview') }}</h2>
        </div>

        <div class="cards-grid">
            <div class="card">
                <div class="card-info">
                    <h3>${{ number_format($totalRevenue, 2) }}</h3>
                    <p data-i18n="totalRevenue">{{ __('totalRevenue') }}</p>
                </div>
                <div class="card-icon bg-green"><i class="fa-solid fa-sack-dollar"></i></div>
            </div>
            <div class="card">
                <div class="card-info">
                    <h3>${{ number_format($monthRevenue, 2) }}</h3>
                    <p data-i18n="monthRevenue">{{ __('monthRevenue') }}</p>
                </div>
                <div class="card-icon bg-blue"><i class="fa-solid fa-calendar-day"></i></div>
            </div>
            <div class="card">
                <div class="card-info">
                    <h3>${{ number_format($todayRevenue, 2) }}</h3>
                    <p data-i18n="todayRevenue">{{ __('todayRevenue') }}</p>
                </div>
                <div class="card-icon bg-purple"><i class="fa-solid fa-money-bill-wave"></i></div>
            </div>
        </div>

        <div class="table-container">
            <h3 data-i18n="recentTransactions">{{ __('recentTransactions') }}</h3>
            <table>
                <thead>
                    <tr>
                        <th data-i18n="transactionId">{{ __('transactionId') }}</th>
                        <th data-i18n="user">{{ __('user') }}</th>
                        <th data-i18n="booking">{{ __('booking') }}</th>
                        <th data-i18n="amount">{{ __('amount') }}</th>
                        <th data-i18n="status">{{ __('status') }}</th>
                        <th data-i18n="date">{{ __('date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPayments as $payment)
                        <tr>
                            <td>#{{ substr($payment->transaction_id, -8) }}</td>
                            <td>{{ $payment->booking->user->name ?? 'N/A' }}</td>
                            <td>{{ $payment->booking->car->name ?? 'N/A' }}</td>
                            <td style="color:var(--primary-color); font-weight:bold;">${{ number_format($payment->amount, 2) }}
                            </td>
                            <td><span class="status bg-green">{{ $payment->status }}</span></td>
                            <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center">{{ __('noTransactionsFound') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div style="padding: 20px;">
                {{ $recentPayments->links() }}
            </div>
        </div>
    </section>
@endsection