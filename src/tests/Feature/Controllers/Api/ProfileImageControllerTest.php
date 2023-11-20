<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
uses(\Tests\TestCase::class);

test('updating image', function () {
    $user = \App\Models\User::factory()->create();

    // Send a PATCH request to the image profile API endpoint
    $response = $this
        ->actingAs($user)
        ->patchJson(route('api.user.image.update'), [
            'image' => \Illuminate\Http\UploadedFile::fake()->create('avatar.jpg', mimeType: 'image/jpeg'),
        ]);

    // Assert that the response has a 200 OK status code
    $response->assertOk();

    // Assert that the response JSON data contains the expected user data
    $response->assertJson((new \App\Http\Resources\UserResource(\App\Models\User::query()->first()))
        ->toResponse(new \Illuminate\Http\Request())
        ->getData(true));

    $resultPath = $user->fresh()->image;

    $response->assertJson([
        'data' => [
            'image' => asset('storage/'.$resultPath),
        ],
    ]);

    // Get the directory contents
    $directoryContents = Storage::listContents(\App\Services\ImageService::PROFILES_IMAGES_PATH)->toArray();
    expect(count($directoryContents))->toEqual(1);

    // Check if the image was stored correctly
    $filePath = $directoryContents[0]->path();
    expect($resultPath)->toEqual($filePath);

    // Delete the image file from storage
    Storage::delete($filePath);
});

test('updating image throwing unauthorized if not authenticated', function () {
    $response = $this->patchJson(route('api.user.image.update'));
    $response->assertUnauthorized();
});

test('deleting image', function () {
    $user = \App\Models\User::factory()->create([
        'image' => 'avatar.jpg',
    ]);

    // Send a DELETE request to the image profile API endpoint
    $response = $this
        ->actingAs($user)
        ->deleteJson(route('api.user.image.destroy'));

    // Assert that the response has a 200 OK status code
    $response->assertOk();

    // Assert that the response JSON data contains the expected user data
    $response->assertJson((new \App\Http\Resources\UserResource(\App\Models\User::query()->first()))
        ->toResponse(new \Illuminate\Http\Request())
        ->getData(true));

    expect($user->fresh()->image)->toEqual(null);

    $response->assertJson([
        'data' => [
            'image' => null,
        ],
    ]);
});

test('deleting image throwing unauthorized if not authenticated', function () {
    $response = $this->deleteJson(route('api.user.image.destroy'));
    $response->assertUnauthorized();
});
