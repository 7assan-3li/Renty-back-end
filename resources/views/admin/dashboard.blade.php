@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <h1>{{ __('dashboard') }}</h1>
    </div>

    <!-- Stats Cards -->
    <div class="cards-grid">
        <div class="card">
            <div>
                <h3>{{ $stats['totalRevenue'] }} SAR</h3>
                <p>{{ __('totalRevenue') }}</p>
            </div>
            <div class="card-icon bg-green"><i class="fa-solid fa-dollar-sign"></i></div>
        </div>
        <div class="card">
            <div>
                <h3>{{ $stats['totalBookings'] }}</h3>
                <p>{{ __('totalBookings') }}</p>
            </div>
            <div class="card-icon bg-blue"><i class="fa-solid fa-calendar-check"></i></div>
        </div>
        <div class="card">
            <div>
                <h3>{{ $stats['totalCars'] }}</h3>
                <p>{{ __('totalCars') }}</p>
            </div>
            <div class="card-icon bg-orange"><i class="fa-solid fa-car"></i></div>
        </div>
        <div class="card">
            <div>
                <h3>{{ $stats['totalUsers'] }}</h3>
                <p>{{ __('totalUsers') }}</p>
            </div>
            <div class="card-icon bg-purple"><i class="fa-solid fa-users"></i></div>
        </div>
    </div>

    <div class="row" style="display:flex; gap:20px; flex-wrap:wrap;">
        <!-- Left Column: Revenue Chart -->
        <div class="col-lg-8" style="flex:2; min-width:300px;">
            <div class="card" style="display:block;">
                <div class="card-header" style="border-bottom:1px solid #eee; padding-bottom:15px; margin-bottom:15px;">
                    <h3>{{ __('revenueOverview') }}</h3>
                </div>
                <div style="height: 300px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Right Column: Top Cars -->
        <div class="col-lg-4" style="flex:1; min-width:300px;">
            <div class="card" style="display:block;">
                <div class="card-header" style="border-bottom:1px solid #eee; padding-bottom:15px; margin-bottom:15px;">
                    <h3>{{ __('topRentedCars') }}</h3>
                </div>
                <ul style="list-style:none; padding:0;">
                    @foreach($topCars as $car)
                        <li
                            style="display:flex; align-items:center; gap:10px; margin-bottom:15px; border-bottom:1px solid #f9f9f9; padding-bottom:10px;">
                            <img src="{{ asset('storage/' . $car->image) }}"
                                style="width:50px; height:50px; border-radius:8px; object-fit:cover;">
                            <div>
                                <h4 style="margin:0; font-size:14px;">{{ $car->name }}</h4>
                                <span style="font-size:12px; color:#888;">{{ $car->bookings_count }} {{ __('bookings') }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="table-container" style="margin-top:20px;">
        <div class="section-header">
            <h3>{{ __('recentBookings') }}</h3>
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline">{{ __('viewAll') }}</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>{{ __('opNumber') }}</th>
                    <th>{{ __('user') }}</th>
                    <th>{{ __('vehicle') }}</th>
                    <th>{{ __('price') }}</th>
                    <th>{{ __('status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentBookings as $booking)
                    <tr>
                        <td>#{{ $booking->id }}</td>
                        <td>{{ $booking->user->name }}</td>
                        <td>{{ $booking->car ? $booking->car->name : 'Deleted' }}</td>
                        <td>{{ $booking->total_price }}</td>
                        <td>
                            @if($booking->status == 'pending')
                                <span class="badge bg-warning text-dark">{{ __('pending') }}</span>
                            @elseif($booking->status == 'confirmed')
                                <span class="badge bg-success">{{ __('confirmed') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ $booking->status }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: '{{ __('revenue') }}',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#008b96',
                    backgroundColor: 'rgba(0, 139, 150, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f0f0f0' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
@endsection