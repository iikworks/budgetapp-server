<?php

use App\DTO\User\StoreUserDTO;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

test('successful sign up', function () {
    // Create a new instance of StoreUserDTO
    $dto = new StoreUserDTO();
    $dto->firstName = 'John';
    $dto->phoneNumber = '+1234567890';
    $dto->password = 'password';

    // Send a POST request to the sign-up API endpoint with the user data
    $response = $this->postJson(route('api.auth.sign-up'), [
        'first_name' => $dto->firstName,
        'phone_number' => $dto->phoneNumber,
        'password' => $dto->password,
    ]);

    // Assert that the response has a 200 OK status code
    $response->assertOk();

    // Assert that the response JSON data contains the expected user data
    $response->assertJson([
        'data' => [
            'user' => (new UserResource(User::query()->first()))
                ->toResponse(new Request())
                ->getData(true)['data'],
        ],
    ]);
});

test('sign up validations', function () {
    $userWithBusyPhoneNumber = User::factory()->create();

    // Define the test cases and loop through each test case
    collect([
        'phone_number_not_provided' => [
            'data' => [
                'first_name' => 'John',
                'password' => 'password',
            ],
            'error_in' => 'phone_number',
            'message' => 'The Phone number field is required.',
        ],
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
        'first_name_not_provided' => [
            'data' => [
                'phone_number' => '+1234567890',
                'password' => 'password',
            ],
            'error_in' => 'first_name',
            'message' => 'The First name field is required.',
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
        'password_not_provided' => [
            'data' => [
                'phone_number' => '+1234567890',
                'first_name' => 'John',
            ],
            'error_in' => 'password',
            'message' => 'The Password field is required.',
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
    ])->each(function (array $case) {
        // Send a POST request to the sign-up route with the test data
        $response = $this->postJson(route('api.auth.sign-up'), $case['data']);

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
