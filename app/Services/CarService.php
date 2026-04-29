<?php

namespace App\Services;

use App\Models\Car;
use App\Constants\CarStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CarService
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Get all cars with their category.
     */
    public function getAll(array $filters = [])
    {
        $query = Car::with('category')
            ->where('status', CarStatus::AVAILABLE)
            ->whereDoesntHave('bookings', function ($q) {
                $q->where('payment_status', 'paid')
                  ->where('finished', 'No');
            })
            ->latest();

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('model', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['min_price'])) {
            $query->where('price_per_day', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price_per_day', '<=', $filters['max_price']);
        }

        return $query->get();
    }



    /**
     * Get a car by ID.
     */
    public function getById($id)
    {
        return Car::with('category')->findOrFail($id);
    }

    /**
     * Store a new car.
     */
    public function store(array $data)
    {
        if (isset($data['image'])) {
            $images = $this->imageService->processImage($data['image'], 'cars');
            $data['image'] = json_encode($images);
        }

        return Car::create($data);
    }

    /**
     * Update a car.
     */
    public function update(Car $car, array $data)
    {
        if (isset($data['image'])) {
            // Delete old image if exists
            if ($car->image) {
                $this->imageService->deleteImage($car->image);
            }
            $images = $this->imageService->processImage($data['image'], 'cars');
            $data['image'] = json_encode($images);
        }

        $car->update($data);

        return $car;
    }

    /**
     * Delete a car.
     */
    public function delete(Car $car)
    {
        if ($car->image) {
            $this->imageService->deleteImage($car->image);
        }

        return $car->delete();
    }

    /**
     * Get statistics for cars.
     */
    public function getStats()
    {
        return [
            'total' => Car::count(),
            'available' => Car::where('status', CarStatus::AVAILABLE)->count(),
            'rented' => Car::where('status', CarStatus::BOOKED)->count(),
            'maintenance' => Car::where('status', CarStatus::MAINTENANCE)->count(),
        ];
    }

    public function toggleFavorite($userId, $carId)
    {
        $user = \App\Models\User::findOrFail($userId);
        return $user->favorites()->toggle($carId);
    }

    public function getFavorites($userId)
    {
        return \App\Models\User::findOrFail($userId)
            ->favorites()
            ->where('status', CarStatus::AVAILABLE)
            ->whereDoesntHave('bookings', function ($q) {
                $q->where('payment_status', 'paid')
                  ->where('finished', 'No');
            })
            ->with('category')
            ->get();
    }
}
