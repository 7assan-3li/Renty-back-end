<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    protected $manager;

    public function __construct()
    {
        // Use GD driver as it's common in XAMPP
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Process an uploaded image and generate multiple variants in WebP format.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return array Paths of the generated images
     */
    public function processImage(UploadedFile $file, string $directory): array
    {
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $timestamp = time();
        $baseName = "{$filename}_{$timestamp}";
        
        // 1. الصورة الأساسية (للتفاصيل) - عرض 600 بكسل (كافٍ جداً للموبايل وسريع)
        $mainPath = "{$directory}/{$baseName}_main.webp";
        $mainImage = $this->manager->decode($file);
        if ($mainImage->width() > 600) {
            $mainImage->scale(width: 600);
        }
        $mainEncoded = $mainImage->encodeUsingFileExtension('webp', quality: 60);
        Storage::disk('public')->put($mainPath, (string) $mainEncoded);

        // 2. الصورة المصغرة (للقوائم والسرعة القصوى) - عرض 150 بكسل
        $thumbPath = "{$directory}/{$baseName}_thumb.webp";
        $thumbImage = $this->manager->decode($file);
        $thumbImage->cover(150, 150); // قص مربع صغير جداً
        $thumbEncoded = $thumbImage->encodeUsingFileExtension('webp', quality: 50);
        Storage::disk('public')->put($thumbPath, (string) $thumbEncoded);

        // إرجاع المسارات
        return [
            'original'  => $mainPath,
            'medium'    => $mainPath,
            'small'     => $thumbPath,
            'thumbnail' => $thumbPath,
        ];
    }

    /**
     * Delete all variants of an image.
     *
     * @param string|array|null $imagePath
     */
    public function deleteImage($imageData): void
    {
        if (is_string($imageData)) {
            // If it's a JSON string from DB
            $imageData = json_decode($imageData, true);
        }

        if (is_array($imageData)) {
            foreach ($imageData as $path) {
                Storage::disk('public')->delete($path);
            }
        } elseif ($imageData) {
            Storage::disk('public')->delete($imageData);
        }
    }
}
