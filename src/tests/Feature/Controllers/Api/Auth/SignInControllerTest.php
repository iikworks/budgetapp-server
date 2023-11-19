<?php

namespace Tests\Feature\Controllers\Api\Auth;

use App\DTO\Auth\SignInCredentialsDTO;
use App\DTO\Auth\SignInResultDTO;
use App\Exceptions\Auth\AuthenticateFailedException;
use App\Http\Resources\Auth\SignInResultResource;
use App\Models\User;
use App\Services\AuthenticateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Mockery\MockInterface;
use Tests\TestCase;

class SignInControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test case for successful sign in.
     */
    public function test_successful_sign_in(): void
    {
        // Create a user using the factory
        $user = User::factory()->create();

        // Create a DTO object for sign in credentials
        $dto = new SignInCredentialsDTO();
        $dto->phoneNumber = '+123456789';
        $dto->password = 'password';

        // Create a result object for sign in
        $result = new SignInResultDTO();
        $result->token = 'token';
        $result->user = $user;

        // Mock the AuthenticateService and define expected behavior
        $this->mock(AuthenticateService::class, function (MockInterface $mock) use ($result) {
            $mock->shouldReceive('authenticateViaCredentials')
                ->once()
                ->andReturn($result);
        });

        // Send a POST request to the sign-in route with the sign-in credentials
        $response = $this->postJson(route('api.auth.sign-in'), [
            'phone_number' => $dto->phoneNumber,
            'password' => $dto->password,
        ]);

        // Assert that the response is successful
        $response->assertOk();

        // Assert that the response JSON matches the expected result
        $response->assertJson(
            (new SignInResultResource($result))
                ->toResponse(new Request())
                ->getData(true),
        );
    }

    /**
     * Test case to verify that sign in fails.
     */
    public function test_sign_in_failed(): void
    {
        // Create a new instance of SignInCredentialsDTO
        $dto = new SignInCredentialsDTO();
        $dto->phoneNumber = '+123456789';
        $dto->password = 'password';

        // Mock the AuthenticateService and set expectations
        $this->mock(AuthenticateService::class, function (MockInterface $mock) {
            $mock->shouldReceive('authenticateViaCredentials')
                ->once()
                ->andThrow(AuthenticateFailedException::class);
        });

        // Send a POST request to the sign-in API endpoint with the credentials
        $response = $this->postJson(route('api.auth.sign-in'), [
            'phone_number' => $dto->phoneNumber,
            'password' => $dto->password,
        ]);

        // Assert that the response status code is 401 Unauthorized
        $response->assertUnauthorized();

        // Assert that the response JSON contains the expected message
        $response->assertJson([
            'message' => 'Authentication failed.',
        ]);
    }

    /**
     * Test the sign-in validations.
     */
    public function test_sign_in_validations(): void
    {
        // Define the test cases and loop through each test case
        collect([
            'phone_number_not_provided' => [
                'data' => [
                    'password' => 'password',
                ],
                'error_in' => 'phone_number',
                'message' => 'The phone number field is required.',
            ],
            'password_not_provided' => [
                'data' => [
                    'phone_number' => '+123456789',
                ],
                'error_in' => 'password',
                'message' => 'The password field is required.',
            ],
        ])->each(function (array $case) {
            // Send a POST request to the sign-in route with the test data
            $response = $this->postJson(route('api.auth.sign-in'), $case['data']);

            // Assert that the response has a status code of 422 (Unprocessable Entity)
            $response->assertUnprocessable();

            // Assert that the response contains the expected validation error
            $response->assertJsonValidationErrors([
                $case['error_in'] => [
                    $case['message'],
                ],
            ]);
        });
    }
}
