<?php

namespace Tests\Feature\Services;

use App\DTO\Auth\SignInCredentialsDTO;
use App\Exceptions\Auth\AuthenticateFailedException;
use App\Models\User;
use App\Services\AuthenticateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticateServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the authentication of a user.
     */
    public function test_authenticate_user(): void
    {
        // Create a new user using the User factory
        $user = User::factory()->create();

        // Get an instance of the AuthenticateService
        $service = $this->app->make(AuthenticateService::class);

        // Authenticate the user
        $result = $service->authenticateUser($user);

        // Assert that the result is a string token
        $this->assertIsString($result->token);

        // Assert that the user ID in the result matches the user ID
        $this->assertEquals($user->id, $result->user->id);

        // Assert that the user has one token in the database
        $this->assertEquals(1, $user->tokens()->count());
    }

    /**
     * Test authentication via credentials.
     *
     * @throws AuthenticateFailedException When the authentication fails.
     */
    public function test_authenticate_via_credentials(): void
    {
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
        $this->assertIsString($result->token);

        // Assert that the user ID in the result matches the created user ID
        $this->assertEquals($user->id, $result->user->id);

        // Assert that the user has one token associated with them
        $this->assertEquals(1, $user->tokens()->count());
    }

    /**
     * Test case for authenticating via credentials and failing by phone number.
     */
    public function test_authenticate_via_credentials_failed_by_phone_number(): void
    {
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
            $this->assertEquals(0, $user->tokens()->count());
            throw $exception;
        }
    }

    /**
     * Test case for authenticating via credentials and failing by password.
     */
    public function test_authenticate_via_credentials_failed_by_password(): void
    {
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
            $this->assertEquals(0, $user->tokens()->count());
            throw $exception;
        }
    }
}
