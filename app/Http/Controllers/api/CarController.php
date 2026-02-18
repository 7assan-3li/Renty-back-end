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
        return response()->json(CarResource::collection($cars));
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
}