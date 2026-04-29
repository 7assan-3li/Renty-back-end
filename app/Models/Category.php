<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    
    protected $appends = ['images_urls'];

    protected $fillable = [
        'name',
        'image',
        'description',
    ];


    public function cars()
    {
        return $this->hasMany(Car::class);
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
            $path = $this->image;
            if (str_starts_with($path, 'public/')) {
                $path = str_replace('public/', '', $path);
            }
            return [
                'original' => url('storage/' . $path),
                'thumbnail' => url('storage/' . $path),
                'small' => url('storage/' . $path),
                'medium' => url('storage/' . $path),
            ];
        }

        // If it's an indexed array
        if (isset($images[0]) && !isset($images['original'])) {
            return [
                'original' => url('storage/' . $images[0]),
                'thumbnail' => url('storage/' . $images[0]),
                'small' => url('storage/' . $images[0]),
                'medium' => url('storage/' . $images[0]),
            ];
        }

        return array_map(function ($path) {
            // استبدال أي مائل عكسي (Windows style) بمائل عادي للروابط
            $path = str_replace('\\', '/', $path);
            return \Illuminate\Support\Facades\Storage::disk('public')->url($path);
        }, $images);
    }
}
