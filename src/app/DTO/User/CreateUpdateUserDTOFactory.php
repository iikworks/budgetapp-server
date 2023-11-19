<?php

namespace App\DTO\User;

readonly class CreateUpdateUserDTOFactory
{
    /**
     * Creates a new DTO object from an array of data.
     *
     * @param array{
     *     first_name: ?string,
     *     phone_number: ?string,
     *     password: ?string,
     * } $data The array of data containing the user's information.
     * @return StoreUserDTO The newly created StoreUserDto object.
     */
    public static function fromArray(array $data): StoreUserDTO
    {
        $dto = new StoreUserDTO();
        $dto->firstName = $data['first_name'] ?? null;
        $dto->phoneNumber = $data['phone_number'] ?? null;
        $dto->password = $data['password'] ?? null;

        return $dto;
    }
}
