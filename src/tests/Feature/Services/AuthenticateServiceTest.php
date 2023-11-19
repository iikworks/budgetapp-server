<?php

use App\DTO\Auth\SignInCredentialsDTO;
use App\Exceptions\Auth\AuthenticateFailedException;
use App\Models\User;
use App\Services\AuthenticateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

test('authenticate user', function () {
    // Create a new user using the User factory
    $user = User::factory()->create();

    // Get an instance of the AuthenticateService
    $service = $this->app->make(AuthenticateService::class);

    // Authenticate the user
    $result = $service->authenticateUser($user);

    // Assert that the result is a string token
    expect($result->token)->toBeString();

    // Assert that the user ID in the result matches the user ID
    expect($result->user->id)->toEqual($user->id);

    // Assert that the user has one token in the database
    expect($user->tokens()->count())->toEqual(1);
});

test('authenticate via credentials', function () {
    // Create a new user using the User factory
    $user = User::factory()->create();

    // Instantiate the AuthenticateService
    $service = $this->app->make(AuthenticateService::class);

    // Create a new DTO object to hold the sign-in credentials
    $dto = new SignInCredentialsDTO();

    // Set the phone number and password in the DTO object
    $dto->phoneNumber = $user->phone_number;
    $dto->password = 'password';

    // Authenticate the user via credentials using the service
    $result = $service->authenticateViaCredentials($dto);

    // Assert that the result token is a string
    expect($result->token)->toBeString();

    // Assert that the user ID in the result matches the created user ID
    expect($result->user->id)->toEqual($user->id);

    // Assert that the user has one token associated with them
    expect($user->tokens()->count())->toEqual(1);
});

test('authenticate via credentials failed by phone number', function () {
    // Create a user
    $user = User::factory()->create();

    // Get the AuthenticateService instance
    $service = $this->app->make(AuthenticateService::class);

    // Create a new SignInCredentialsDTO with wrong phone number and password
    $dto = new SignInCredentialsDTO();
    $dto->phoneNumber = 'wrong';
    $dto->password = 'password';

    // Expect an AuthenticateFailedException to be thrown
    $this->expectException(AuthenticateFailedException::class);

    try {
        // Call the authenticateViaCredentials method with the DTO
        $service->authenticateViaCredentials($dto);
    } catch (AuthenticateFailedException $exception) {
        // Assert that the user has no tokens
        expect($user->tokens()->count())->toEqual(0);
        throw $exception;
    }
});

test('authenticate via credentials failed by password', function () {
    // Create a user
    $user = User::factory()->create();

    // Get the AuthenticateService instance
    $service = $this->app->make(AuthenticateService::class);

    // Create a new SignInCredentialsDTO with phone number and wrong password
    $dto = new SignInCredentialsDTO();
    $dto->phoneNumber = $user->phone_number;
    $dto->password = 'wrong';

    // Expect an AuthenticateFailedException to be thrown
    $this->expectException(AuthenticateFailedException::class);

    try {
        // Call the authenticateViaCredentials method with the DTO
        $service->authenticateViaCredentials($dto);
    } catch (AuthenticateFailedException $exception) {
        // Assert that the user has no tokens
        expect($user->tokens()->count())->toEqual(0);
        throw $exception;
    }
});
