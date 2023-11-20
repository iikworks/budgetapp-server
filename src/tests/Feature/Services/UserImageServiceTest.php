<?php

uses(\Tests\TestCase::class);
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('update', function () {
    // Create a fake uploaded file and initialize the UserImageService
    $user = \App\Models\User::factory()->create();
    $service = $this->app->make(\App\Services\UserImageService::class);

    // Update the user's image
    $updatedUser = $service->update($user, 'image.jpg');

    // Check if the image was updated correctly
    expect($updatedUser->image)->toEqual('image.jpg');
});

it('delete', function () {
    // Create a fake uploaded file and initialize the UserImageService
    $user = \App\Models\User::factory()->create();
    $service = $this->app->make(\App\Services\UserImageService::class);

    // Delete the user's image
    $updatedUser = $service->update($user, null);

    // Check if the image was updated correctly
    expect($updatedUser->image)->toBeNull();
});
