<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'model' => $this->model,
            'description' => $this->description,
            'images_urls' => $this->getImages(),
            'images' => $this->getImages(), // Added for compatibility
            'image' => $this->image,
            'price_per_day' => (float) $this->price_per_day,
            'price' => $this->price_per_day, // Added for compatibility
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'rating' => (float) $this->rating,
            'rating_count' => (int) $this->rating_count,
            'counter' => (int) $this->counter,
            'status' => $this->status,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'is_favorited' => $request->user() ? $this->resource->favoritedBy()->where('user_id', $request->user()->id)->exists() : false,
            'stars_count' => (int) ($this->stars_count ?? 0),
            'is_starred' => $request->user() ? $this->resource->starredBy()->where('user_id', $request->user()->id)->exists() : false,
        ];
    }
}
