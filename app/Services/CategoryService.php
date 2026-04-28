<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Car;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Get all categories.
     */
    public function getAll()
    {
        return Category::all();
    }

    /**
     * Store a new category.
     */
    public function store(array $data)
    {
        if (isset($data['image'])) {
            $images = $this->imageService->processImage($data['image'], 'categories');
            $data['image'] = json_encode($images);
        }

        return Category::create($data);
    }

    /**
     * Update a category.
     */
    public function update(Category $category, array $data)
    {
        if (isset($data['image'])) {
            // Delete old images if exists
            if ($category->image) {
                $this->imageService->deleteImage($category->image);
            }
            $images = $this->imageService->processImage($data['image'], 'categories');
            $data['image'] = json_encode($images);
        }

        $category->update($data);

        return $category;
    }

    /**
     * Delete a category.
     */
    public function delete(Category $category)
    {
        if ($category->image) {
            $this->imageService->deleteImage($category->image);
        }

        return $category->delete();
    }

    public function getStats()
    {
        return [
            'total_categories' => Category::count(),
            'total_vehicles' => Car::count(),
            'available_vehicles' => Car::where('status', 'available')->count(),
            'rented_vehicles' => Car::where('status', 'rented')->count(),
        ];
    }
}
