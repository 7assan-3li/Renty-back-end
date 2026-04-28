@extends('layouts.admin')

@section('content')
    <div class="section-header">
        <h2>Category Details</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="cards-grid">
        <div class="card" style="display: block;">
            <div style="display: flex; gap: 20px; align-items: flex-start;">
                @if($category->image)
                    @php
                        $categoryImages = $category->getImages();
                        $original = $categoryImages['original'] ?? null;
                    @endphp
                    @if($original)
                        <img src="{{ $original }}" alt="{{ $category->name }}" style="width: 150px; height: 150px; object-fit: cover; border-radius: 10px;">
                    @else
                        <div style="width: 150px; height: 150px; background: #eee; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #999;">
                            <i class="fa-regular fa-image"></i>
                        </div>
                    @endif
                @else
                    <div style="width: 150px; height: 150px; background: #eee; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #999;">
                        <i class="fa-regular fa-image"></i>
                    </div>
                @endif
                <div>
                    <h3 style="margin-bottom: 10px; color: var(--primary-color);">{{ $category->name }}</h3>
                    <p style="color: #666; line-height: 1.6;">{{ $category->description ?? 'No description available.' }}</p>
                    <div style="margin-top: 15px; font-size: 13px; color: #999;">
                        Created at: {{ $category->created_at->format('F j, Y, g:i a') }}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-info">
                <h3>{{ $category->cars->count() }}</h3>
                <p>Vehicles in Category</p>
            </div>
            <div class="card-icon bg-blue">
                <i class="fa-solid fa-car"></i>
            </div>
        </div>
    </div>

    <div class="table-container">
        <div class="section-header">
            <h3>Associated Vehicles</h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Model</th>
                    <th>Price/Day</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($category->cars as $car)
                    <tr>
                        <td>
                            @if($car->image)
                                @php
                                    $carImages = $car->getImages();
                                    $thumbnail = $carImages['thumbnail'] ?? null;
                                @endphp
                                @if($thumbnail)
                                    <img src="{{ $thumbnail }}" alt="{{ $car->name }}" style="width: 40px; height: 40px; border-radius: 5px; object-fit: cover;">
                                @else
                                    <div style="width: 40px; height: 40px; background: #eee; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #999;">
                                        <i class="fa-solid fa-car"></i>
                                    </div>
                                @endif
                            @else
                                <div style="width: 40px; height: 40px; background: #eee; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #999;">
                                    <i class="fa-solid fa-car"></i>
                                </div>
                            @endif
                        </td>
                        <td style="font-weight: bold;">{{ $car->name }}</td>
                        <td>{{ $car->model }}</td>
                        <td>${{ number_format($car->price_per_day, 2) }}</td>
                        <td>
                            <span class="status {{ $car->status === 'available' ? 'bg-green' : ($car->status === 'rented' ? 'bg-blue' : 'bg-red') }}">
                                {{ ucfirst($car->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px; color: #777;">
                            No vehicles found in this category.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
