@extends('layouts.admin')

@section('content')
    <div class="section-header">
        <h2>Vehicle Details</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.cars.edit', $car->id) }}" class="btn">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.cars.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <div class="cards-grid">
        <div class="card" style="display: block; grid-column: span 2;">
            <div style="display: flex; gap: 30px; align-items: flex-start;">
                @if($car->image)
                    @php
                        $carImages = $car->getImages();
                        $original = $carImages['original'] ?? null;
                    @endphp
                    @if($original)
                        <img src="{{ $original }}" alt="{{ $car->name }}"
                            style="width: 300px; height: 200px; object-fit: cover; border-radius: 10px;">
                    @else
                        <div
                            style="width: 300px; height: 200px; background: #eee; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #999;">
                            <i class="fa-solid fa-car"></i>
                        </div>
                    @endif
                @else
                    <div
                        style="width: 300px; height: 200px; background: #eee; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #999;">
                        <i class="fa-solid fa-car"></i>
                    </div>
                @endif
                <div style="flex: 1;">
                    <h3 style="margin-bottom: 10px; color: var(--primary-color); font-size: 24px;">{{ $car->name }}</h3>
                    <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                        <span
                            style="background: #f0f0f0; padding: 5px 10px; border-radius: 5px; font-weight: bold; color: #555;">{{ $car->model }}</span>
                        <span
                            class="status {{ $car->status === \App\Constants\CarStatus::AVAILABLE ? 'bg-green' : ($car->status === \App\Constants\CarStatus::BOOKED ? 'bg-orange' : 'bg-red') }}">
                            {{ ucfirst($car->status) }}
                        </span>
                        @if($car->category)
                            <span
                                style="background: #e8eaf6; color: #3f51b5; padding: 5px 10px; border-radius: 5px; font-weight: bold;">
                                {{ $car->category->name }}
                            </span>
                        @endif
                    </div>

                    <h4 style="margin-bottom: 10px; color: #555;">Price</h4>
                    <p style="font-size: 20px; font-weight: bold; color: var(--primary-color); margin-bottom: 20px;">
                        ${{ number_format($car->price_per_day, 2) }} <span
                            style="font-size: 14px; color: #777; font-weight: normal;">/ day</span>
                    </p>

                    <h4 style="margin-bottom: 10px; color: #555;">Location</h4>
                    <div id="map" style="height: 200px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd;">
                    </div>
                    <p style="color: #666; margin-bottom: 20px;">
                        <i class="fa-solid fa-location-dot" style="color: var(--danger);"></i>
                        {{ $car->latitude }}, {{ $car->longitude }}
                    </p>

                    <h4 style="margin-bottom: 10px; color: #555;">Description</h4>
                    <p style="color: #666; line-height: 1.6;">{{ $car->description ?? 'No description available.' }}</p>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var lat = {{ $car->latitude }};
                var lng = {{ $car->longitude }};
                var map = L.map('map').setView([lat, lng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                L.marker([lat, lng]).addTo(map)
                    .bindPopup('<b>{{ $car->name }}</b><br>Current Location')
                    .openPopup();
            });
        </script>

        <div class="card">
            <div class="card-info">
                <h3>{{ $car->bookings->count() }}</h3>
                <p>Total Bookings</p>
            </div>
            <div class="card-icon bg-blue">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>

        <div class="card">
            <div class="card-info">
                <h3>${{ number_format($car->bookings->sum('total_price'), 2) }}</h3>
                <p>Total Earnings</p>
            </div>
            <div class="card-icon bg-green">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
        </div>
    </div>
@endsection