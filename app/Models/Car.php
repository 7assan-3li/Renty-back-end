<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Car extends Model
{
    use HasFactory;
    
    protected $appends = ['images_urls', 'is_starred'];

    protected $fillable = [
        'name',
        'image',
        'description',
        'model',
        'latitude',
        'longitude',
        'price_per_day',
        'rating',
        'rating_count',
        'counter',
        'status',
        'category_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'car_id', 'user_id')->withTimestamps();
    }

    public function starredBy()
    {
        return $this->belongsToMany(User::class, 'car_stars', 'car_id', 'user_id')->withTimestamps();
    }

    public function getIsStarredAttribute()
    {
        $userId = auth('sanctum')->id();
        if (!$userId) return false;
        return $this->starredBy()->where('user_id', $userId)->exists();
    }

    public function getImagesUrlsAttribute()
    {
        return $this->getImages();
    }

    public function getImages()
    {
        if (!$this->image) {
            return null;
        }

        $images = json_decode($this->image, true);

        if (!is_array($images)) {
            // Fallback for old single image string
            return [
                'original' => url('storage/' . $this->image),
                'thumbnail' => url('storage/' . $this->image),
                'small' => url('storage/' . $this->image),
                'medium' => url('storage/' . $this->image),
            ];
        }

        return array_map(function ($path) {
            return url('storage/' . $path);
        }, $images);
    }
}
