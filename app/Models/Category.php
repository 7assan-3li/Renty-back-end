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

        return array_map(function ($path) {
            return url('storage/' . $path);
        }, $images);
    }
}
