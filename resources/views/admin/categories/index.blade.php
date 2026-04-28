@extends('layouts.admin')

@section('content')
    <div class="cards-grid" style="margin-bottom: 30px;">
        <div class="card">
            <div class="card-info">
                <h3>{{ $stats['total_categories'] }}</h3>
                <p>{{ __('totalCategories') }}</p>
            </div>
            <div class="card-icon bg-purple">
                <i class="fa-solid fa-layer-group"></i>
            </div>
        </div>
        <div class="card">
            <div class="card-info">
                <h3>{{ $stats['total_vehicles'] }}</h3>
                <p>{{ __('totalVehicles') }}</p>
            </div>
            <div class="card-icon bg-blue">
                <i class="fa-solid fa-car"></i>
            </div>
        </div>
        <div class="card">
            <div class="card-info">
                <h3>{{ $stats['available_vehicles'] }}</h3>
                <p>{{ __('availableVehicles') }}</p>
            </div>
            <div class="card-icon bg-green">
                <i class="fa-solid fa-check-circle"></i>
            </div>
        </div>
        <div class="card">
            <div class="card-info">
                <h3>{{ $stats['rented_vehicles'] }}</h3>
                <p>{{ __('rentedVehicles') }}</p>
            </div>
            <div class="card-icon bg-orange">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>
    </div>

    <div class="section-header">
        <h2>{{ __('categories') }}</h2>
        <a href="{{ route('admin.categories.create') }}" class="btn">
            <i class="fas fa-plus"></i> {{ __('addCategory') }}
        </a>
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
                    <th>{{ __('description') }}</th>
                    <th>{{ __('createdAt') }}</th>
                    <th>{{ __('actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>
                            @if($category->image)
                                @php
                                    $categoryImages = $category->getImages();
                                    $thumbnail = $categoryImages['thumbnail'] ?? null;
                                @endphp
                                @if($thumbnail)
                                    <img src="{{ $thumbnail }}" alt="{{ $category->name }}" style="width: 40px; height: 40px; border-radius: 5px; object-fit: cover;">
                                @else
                                    <div style="width: 40px; height: 40px; background: #eee; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #999;">
                                        <i class="fa-regular fa-image"></i>
                                    </div>
                                @endif
                            @else
                                <div style="width: 40px; height: 40px; background: #eee; border-radius: 5px; display: flex; align-items: center; justify-content: center; color: #999;">
                                    <i class="fa-regular fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td style="font-weight: bold;">{{ $category->name }}</td>
                        <td style="color: #666;">{{ Str::limit($category->description, 50) }}</td>
                        <td>{{ $category->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-outline" style="padding: 5px 10px; color: var(--primary-color); border-color: var(--primary-color);">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-outline" style="padding: 5px 10px;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('{{ __('deleteCategoryConfirm') }}');">
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
                        <td colspan="5" style="text-align: center; padding: 30px; color: #777;">
                            {{ __('noCategoriesFound') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection