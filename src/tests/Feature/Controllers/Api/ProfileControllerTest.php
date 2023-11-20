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

test('updating profile', function () {
    // Create an authenticated user
    $authenticatedUser = User::factory()->create();

    // Send a PATCH request to the user update route with the user data
    $response = $this
        ->actingAs($authenticatedUser)
        ->patchJson(route('api.user.update'), [
            'phone_number' => '+1234567890',
            'first_name' => 'John',
            'password' => 'new_password',
        ]);

    // Assert that the response has a 200 OK status code
    $response->assertOk();

    // Fetch the updated user from the database
    $authenticatedUser->fresh();

    // Assert that the user's data was updated correctly
    expect($authenticatedUser->first_name)->toBe('John');
    expect($authenticatedUser->phone_number)->toBe('+1234567890');
    expect(password_verify('new_password', $authenticatedUser->password))->toBeTrue();
});

test('updating profile partial (update only first name)', function () {
    // Create an authenticated user
    $authenticatedUser = User::factory()->create();

    // Send a PATCH request to the user update route with the user data
    $response = $this
        ->actingAs($authenticatedUser)
        ->patchJson(route('api.user.update'), [
            'first_name' => 'John',
        ]);

    // Assert that the response has a 200 OK status code
    $response->assertOk();

    // Fetch the updated user from the database
    $updatedUser = User::query()->find($authenticatedUser->id);

    // Assert that the user's data was updated correctly
    expect($updatedUser->first_name)->toBe('John');
    expect($updatedUser->phone_number)->toBe($authenticatedUser->phone_number);
    expect(password_verify('password', $updatedUser->password))->toBeTrue();
});

test('updating profile throwing unauthorized if not authenticated', function () {
    $response = $this
        ->patchJson(route('api.user.update'));
    $response->assertUnauthorized();
});

test('updating profile validations', function () {
    $authenticatedUser = User::factory()->create();
    $userWithBusyPhoneNumber = User::factory()->create();

    // Define the test cases and loop through each test case
    collect([
        'phone_number_must_be_unique' => [
            'data' => [
                'phone_number' => $userWithBusyPhoneNumber->phone_number,
                'first_name' => 'John',
                'password' => 'password',
            ],
            'error_in' => 'phone_number',
            'message' => 'The Phone number has already been taken.',
        ],
        'phone_number_must_be_valid' => [
            'data' => [
                'phone_number' => 'wrong_phone_number',
                'first_name' => 'John',
                'password' => 'password',
            ],
            'error_in' => 'phone_number',
            'message' => 'The Phone number field format is invalid.',
        ],
        'first_name_must_be_at_least_2_characters_long' => [
            'data' => [
                'phone_number' => '+1234567890',
                'first_name' => 'J',
                'password' => 'password',
            ],
            'error_in' => 'first_name',
            'message' => 'The First name field must be at least 2 characters.',
        ],
        'first_name_must_be_a_maximum_of_24_characters_long' => [
            'data' => [
                'phone_number' => '+1234567890',
                'first_name' => str_repeat('J', 25),
                'password' => 'password',
            ],
            'error_in' => 'first_name',
            'message' => 'The First name field must not be greater than 24 characters.',
        ],
        'password_must_be_at_least_6_characters_long' => [
            'data' => [
                'phone_number' => '+1234567890',
                'first_name' => 'John',
                'password' => 'pass',
            ],
            'error_in' => 'password',
            'message' => 'The Password field must be at least 6 characters.',
        ],
        'password_must_be_a_maximum_of_124_characters_long' => [
            'data' => [
                'phone_number' => '+1234567890',
                'first_name' => 'John',
                'password' => Str::random(125),
            ],
            'error_in' => 'password',
            'message' => 'The Password field must not be greater than 124 characters.',
        ],
    ])->each(function (array $case) use ($authenticatedUser) {
        // Send a PATCH request to the user update route with the test data
        $response = $this
            ->actingAs($authenticatedUser)
            ->patchJson(route('api.user.update'), $case['data']);

        // Assert that the response has a status code of 422 (Unprocessable Entity)
        $response->assertUnprocessable();

        // Assert that the response contains the expected validation error
        $response->assertJsonValidationErrors([
            $case['error_in'] => [
                $case['message'],
            ],
        ]);
    });
});
