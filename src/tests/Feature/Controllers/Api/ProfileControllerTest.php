<?php

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthenticateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

test('getting profile', function () {
    $user = User::factory()->create();

    // Authenticate the user
    $authenticateService = $this->app->make(AuthenticateService::class);
    $authenticateResult = $authenticateService->authenticateUser($user);

    // Send a GET request to the profile API endpoint
    $response = $this->withHeader('Authorization', 'Bearer '.$authenticateResult->token)
        ->getJson(route('api.user'));

    // Assert that the response has a 200 OK status code
    $response->assertOk();

    // Assert that the response JSON data contains the expected user data
    $response->assertJson((new UserResource(User::query()->first()))
        ->toResponse(new Request())
        ->getData(true));
});

test('getting profile throwing unauthorized if not authenticated', function () {
    $response = $this->getJson(route('api.user'));
    $response->assertUnauthorized();
});
