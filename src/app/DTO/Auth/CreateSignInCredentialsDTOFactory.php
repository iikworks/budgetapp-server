<?php

namespace App\DTO\Auth;

use App\Http\Requests\Auth\SignInRequest;

readonly class CreateSignInCredentialsDTOFactory
{
    /**
     * Creates a new SignInCredentialsDTO object from a SignInRequest object.
     *
     * @param  SignInRequest  $request The Request object.
     * @return SignInCredentialsDTO The newly created SignInCredentialsDTO object.
     */
    public static function fromRequest(SignInRequest $request): SignInCredentialsDTO
    {
        return self::fromArray($request->validated());
    }

    /**
     * Creates a new DTO object from an array of data.
     *
     * @param array{
     *     phone_number: string,
     *     password: string,
     * } $data The data to create the DTO from.
     * @return SignInCredentialsDTO The newly created SignInCredentialsDTO object.
     */
    public static function fromArray(array $data): SignInCredentialsDTO
    {
        $dto = new SignInCredentialsDTO();
        $dto->phoneNumber = $data['phone_number'];
        $dto->password = $data['password'];

        return $dto;
    }
}
