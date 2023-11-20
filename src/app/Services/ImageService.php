<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    public const PROFILES_IMAGES_PATH = 'profiles';

    /**
     * Store an uploaded image file.
     *
     * @param  string  $path The path where the image will be stored.
     * @param  UploadedFile  $image The uploaded image file.
     * @return string The file name of the stored image.
     */
    public function store(string $path, UploadedFile $image): string
    {
        // Generate a unique file name for the image
        $fileName = Str::uuid().'.'.$image->getClientOriginalExtension();

        // Store the image file with the generated file name
        $filePath = $image->storePubliclyAs($path, $fileName);

        Log::info('Image stored.', [
            'op' => 'App\Service\ImageService::store',
            'path' => $filePath,
            'fileName' => $fileName,
        ]);

        // Return the file name of the stored image
        return $filePath;
    }

    /**
     * Deletes the image file.
     *
     * @param  string  $path The path of the image file.
     */
    public function delete(string $path): void
    {
        Storage::delete($path);
    }
}
