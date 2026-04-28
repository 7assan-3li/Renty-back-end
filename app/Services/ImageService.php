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
        
        // 1. Save Original Image (as uploaded, potentially up to 50MB)
        $originalPath = $file->storeAs($directory, "{$baseName}_original." . $file->getClientOriginalExtension(), 'public');

        // 2. Generate Optimized Variants in WebP
        $variants = [
            'thumbnail' => ['width' => 150, 'height' => 150],
            'small'     => ['width' => 400, 'height' => null],
            'medium'    => ['width' => 800, 'height' => null],
        ];

        $paths = [
            'original' => $originalPath,
        ];

        foreach ($variants as $key => $size) {
            $image = $this->manager->decode($file);
            
            if ($size['height']) {
                $image->cover($size['width'], $size['height']);
            } else {
                $image->scale(width: $size['width']);
            }

            $variantPath = "{$directory}/{$baseName}_{$key}.webp";
            
            // Encode as WebP with 80% quality
            $encoded = $image->encodeUsingFileExtension('webp', quality: 80);
            
            Storage::disk('public')->put($variantPath, (string) $encoded);
            
            $paths[$key] = $variantPath;
        }

        return $paths;
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
