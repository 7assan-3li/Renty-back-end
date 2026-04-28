<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Services\CarService;
use Illuminate\Http\Request;

class CarController extends Controller
{
    protected $carService;

    public function __construct(CarService $carService)
    {
        $this->carService = $carService;
    }



    public function index(Request $request)
    {
        $filters = $request->only(['category_id', 'search', 'min_price', 'max_price']);
        $cars = $this->carService->getAll($filters);
        return CarResource::collection($cars);
    }

    public function show($id)
    {
        $car = $this->carService->getById($id);
        return new CarResource($car);
    }

    public function toggleFavorite(Request $request, $id)
    {
        $result = $this->carService->toggleFavorite($request->user()->id, $id);
        // $result['attached'] is array of attached IDs, $result['detached'] is array of detached IDs
        $status = count($result['attached']) > 0 ? 'added' : 'removed';

        return response()->json([
            'message' => 'Car ' . $status . ' to favorites',
            'status' => $status
        ]);
    }

    public function favorites(Request $request)
    {
        $favorites = $this->carService->getFavorites($request->user()->id);
        return CarResource::collection($favorites);
    }

    public function toggleStar(Request $request, $id)
    {
        $car = \App\Models\Car::findOrFail($id);
        $user = $request->user();
        
        $exists = $car->starredBy()->where('user_id', $user->id)->exists();
        
        if ($exists) {
            $car->starredBy()->detach($user->id);
            $car->decrement('stars_count');
            $status = 'removed';
        } else {
            $car->starredBy()->attach($user->id);
            $car->increment('stars_count');
            $status = 'added';
        }
        
        return response()->json([
            'status' => $status,
            'stars_count' => $car->stars_count,
            'is_starred' => !$exists
        ]);
    }
}