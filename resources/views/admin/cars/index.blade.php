@extends('layouts.admin')

@section('content')
    <div class="cards-grid" style="margin-bottom: 30px;">
        <div class="card">
            <div class="card-info">
                <h3>{{ $stats['total'] }}</h3>
                <p>{{ __('totalVehicles') }}</p>
            </div>
            <div class="card-icon bg-blue">
                <i class="fa-solid fa-car"></i>
            </div>
        </div>
        <div class="card">
            <div class="card-info">
                <h3>{{ $stats['available'] }}</h3>
                <p>{{ __('available') }}</p>
            </div>
            <div class="card-icon bg-green">
                <i class="fa-solid fa-check-circle"></i>
            </div>
        </div>
        <div class="card">
            <div class="card-info">
                <h3>{{ $stats['rented'] }}</h3>
                <p>{{ __('rented') }}</p>
            </div>
            <div class="card-icon bg-orange">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>
        <div class="card">
            <div class="card-info">
                <h3>{{ $stats['maintenance'] }}</h3>
                <p>{{ __('maintenance') }}</p>
            </div>
            <div class="card-icon bg-red">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
        </div>
    </div>

    <div class="section-header">
        <h2>{{ __('vehicles') }}</h2>
        <a href="{{ route('admin.cars.create') }}" class="btn">
            <i class="fas fa-plus"></i> {{ __('addNewVehicle') }}
        </a>
    </div>

    <style>
        .category-tabs {
            margin-bottom: 25px;
            display: flex;
            gap: 15px;
            overflow-x: auto;
            border-bottom: 1px solid #eee;
            padding-bottom: 0;
        }

        .category-tab {
            padding: 10px 15px;
            color: #666;
            text-decoration: none;
            font-weight: 500;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .category-tab:hover {
            color: var(--primary-color);
        }

        .category-tab.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
    </style>

    <div class="category-tabs">
        <a href="{{ route('admin.cars.index') }}" 
           class="category-tab {{ !request('category_id') ? 'active' : '' }}">
            {{ __('all') }}
        </a>
        @foreach($categories as $category)
            <a href="{{ route('admin.cars.index', ['category_id' => $category->id]) }}" 
               class="category-tab {{ request('category_id') == $category->id ? 'active' : '' }}">
                {{ $category->name }}
            </a>
        @endforeach
    </div>

    @if(session('success'))
        <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>{{ __('image') }}</th>
                    <th>{{ __('name') }}</th>
                    <th>{{ __('model') }}</th>
                    <th>{{ __('category') }}</th>
                    <th>{{ __('pricePerDay') }}</th>
                    <th>{{ __('status') }}</th>
                    <th>{{ __('actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cars as $car)
                    <tr>
                        <td>
                            @if($car->image)
                                @php
                                    $carImages = $car->getImages();
                                    $thumbnail = $carImages['thumbnail'] ?? null;
                                @endphp
                                @if($thumbnail)
                                    <img src="{{ $thumbnail }}" alt="{{ $car->name }}" style="width: 50px; height: 35px; border-radius: 4px; object-fit: cover;">
                                @else
                                    <div style="width: 50px; height: 35px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #999;">
                                        <i class="fa-solid fa-car"></i>
                                    </div>
                                @endif
                            @else
                                <div style="width: 50px; height: 35px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #999;">
                                    <i class="fa-solid fa-car"></i>
                                </div>
                            @endif
                        </td>
                        <td style="font-weight: bold;">{{ $car->name }}</td>
                        <td>{{ $car->model }}</td>
                        <td>
                            @if($car->category)
                                <span style="background: #f0f0f0; padding: 4px 8px; border-radius: 4px; font-size: 12px; color: #555;">
                                    {{ $car->category->name }}
                                </span>
                            @else
                                <span style="color: #999;">-</span>
                            @endif
                        </td>
                        <td style="font-weight: bold;">${{ number_format($car->price_per_day, 2) }}</td>
                        <td>
                            <span class="status {{ $car->status === \App\Constants\CarStatus::AVAILABLE ? 'bg-green' : ($car->status === \App\Constants\CarStatus::BOOKED ? 'bg-orange' : 'bg-red') }}">
                                {{ ucfirst($car->status) }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('admin.cars.show', $car->id) }}" class="btn btn-outline" style="padding: 5px 10px; color: var(--primary-color); border-color: var(--primary-color);">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.cars.edit', $car->id) }}" class="btn btn-outline" style="padding: 5px 10px;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.cars.destroy', $car->id) }}" method="POST" onsubmit="return confirm('{{ __('deleteVehicleConfirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline" style="padding: 5px 10px; color: #c62828; border-color: #ffcdd2;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: #777;">
                            {{ __('noVehiclesFound') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
