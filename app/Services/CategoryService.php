<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Car;
use Illuminate\Support\Facades\Storage;

class CategoryService
{
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
            $data['image'] = $data['image']->store('categories', 'public');
        }

        return Category::create($data);
    }

    /**
     * Update a category.
     */
    public function update(Category $category, array $data)
    {
        if (isset($data['image'])) {
            // Delete old image if exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $data['image']->store('categories', 'public');
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
            Storage::disk('public')->delete($category->image);
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
