<?php

use App\Dto\User\StoreUserDTO;
use App\Dto\User\UpdateUserDTO;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

test('create', function () {
    // Create an instance of the UserService class.
    $service = $this->app->make(UserService::class);

    // Create a DTO object with user data.
    $dto = new StoreUserDTO();
    $dto->firstName = 'John';
    $dto->phoneNumber = '+1234567890';
    $dto->password = 'password';

    // Call the store method and get the user object.
    $user = $service->create($dto);

    // Assert that the user object is an instance of the User class.
    expect($user)->toBeInstanceOf(User::class);

    // Assert that the user object has the correct first name, phone number, and password.
    expect($user->first_name)->toEqual($dto->firstName);
    expect($user->phone_number)->toEqual($dto->phoneNumber);
    expect(Hash::check('password', $user->password))->toBeTrue();

    // Assert that the user record exists in the database.
    $this->assertDatabaseHas('users', [
        'first_name' => 'John',
        'phone_number' => '+1234567890',
    ]);

    // Assert that there is only one user record in the database.
    expect(User::query()->count())->toEqual(1);
});

test('update', function () {
    // Create a new user using the User factory
    $user = User::factory()->create();

    // Instantiate the UserService
    $service = $this->app->make(UserService::class);

    // Assert that there is only one user in the database
    expect(User::query()->count())->toEqual(1);

    // Create a new UpdateUserDto object with the desired data
    $dto = new UpdateUserDTO();
    $dto->firstName = 'John';
    $dto->phoneNumber = '+1234567890';
    $dto->password = 'password';

    // Update the user using the UserService
    $user = $service->update($user, $dto);

    // Assert that the returned user is an instance of the User class
    expect($user)->toBeInstanceOf(User::class);

    // Assert that the user's first name, phone number, and password are updated correctly
    expect($user->first_name)->toEqual($dto->firstName);
    expect($user->phone_number)->toEqual($dto->phoneNumber);
    expect(Hash::check('password', $user->password))->toBeTrue();

    // Assert that the updated user data is stored in the database
    $this->assertDatabaseHas('users', [
        'first_name' => 'John',
        'phone_number' => '+1234567890',
    ]);

    // Assert that there is still only one user in the database
    expect(User::query()->count())->toEqual(1);
});
