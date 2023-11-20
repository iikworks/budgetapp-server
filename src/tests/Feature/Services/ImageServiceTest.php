<?php

use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class);

it('store', function () {
    // Instantiate the ImageService
    $imageService = $this->app->make(ImageService::class);

    // Create a fake uploaded file
    $uploadedFile = UploadedFile::fake()->create('image.jpg', mimeType: 'image/jpeg');

    // Store the image
    $resultPath = $imageService->store(ImageService::PROFILES_IMAGES_PATH, $uploadedFile);

    // Get the directory contents
    $directoryContents = Storage::listContents(ImageService::PROFILES_IMAGES_PATH)->toArray();
    expect(count($directoryContents))->toEqual(1);

    // Check if the image was stored correctly
    $filePath = $directoryContents[0]->path();
    expect($resultPath)->toEqual($filePath);

    // Delete the image file from storage
    Storage::delete($filePath);
});

it('delete', function () {
    // Instantiate the ImageService
    $imageService = $this->app->make(ImageService::class);

    // Create a fake uploaded file
    $uploadedFile = UploadedFile::fake()->create('image.jpg', mimeType: 'image/jpeg');

    // Store the image
    $resultPath = $imageService->store(ImageService::PROFILES_IMAGES_PATH, $uploadedFile);

    // Get the directory contents
    $directoryContents = Storage::listContents(ImageService::PROFILES_IMAGES_PATH)->toArray();
    expect(count($directoryContents))->toEqual(1);

    // Delete the image
    $imageService->delete($resultPath);

    // Get the directory contents again
    $directoryContents = Storage::listContents(ImageService::PROFILES_IMAGES_PATH)->toArray();
    expect(count($directoryContents))->toEqual(0);
});
