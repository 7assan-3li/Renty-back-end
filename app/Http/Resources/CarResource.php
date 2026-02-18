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
            'price_per_day' => (float) $this->price_per_day,
            'image' => $this->image ? url('storage/' . $this->image) : null,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'rating' => (float) $this->rating,
            'rating_count' => (int) $this->rating_count,
            'counter' => (int) $this->counter,
            'status' => $this->status,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'is_favorited' => $request->user() ? $this->resource->favoritedBy()->where('user_id', $request->user()->id)->exists() : false,
        ];
    }
}
