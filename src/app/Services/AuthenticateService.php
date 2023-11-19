<?php

namespace App\Services;

use App\DTO\Auth\SignInCredentialsDTO;
use App\DTO\Auth\SignInResultDTO;
use App\Exceptions\Auth\AuthenticateFailedException;
use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

class AuthenticateService
{
    /**
     * Authenticates a user.
     *
     * @param  User  $user The user to be authenticated.
     * @return SignInResultDTO The SignInResultDTO containing the authentication token and user details.
     */
    public function authenticateUser(User $user): SignInResultDTO
    {
        $dto = new SignInResultDTO();
        $dto->token = $this->createTokenForUser($user)->plainTextToken;
        $dto->user = $user;

        return $dto;
    }

    /**
     * Authenticate a user via credentials.
     *
     * @param  SignInCredentialsDTO  $dto The data transfer object containing the user's credentials.
     * @return SignInResultDTO The result of the authentication process.
     *
     * @throws AuthenticateFailedException Thrown when the authentication fails.
     */
    public function authenticateViaCredentials(SignInCredentialsDTO $dto): SignInResultDTO
    {
        $user = User::query()
            ->where('phone_number', $dto->phoneNumber)->first();
        if ($user == null) {
            throw new AuthenticateFailedException();
        }

        if (! password_verify($dto->password, $user->password)) {
            throw new AuthenticateFailedException();
        }

        return $this->authenticateUser($user);
    }

    /**
     * Creates a new access token for a user.
     *
     * @param  User  $user The user for whom the access token is created.
     * @return NewAccessToken The newly created access token.
     */
    private function createTokenForUser(User $user): NewAccessToken
    {
        return $user->createToken('authToken');
    }
}
