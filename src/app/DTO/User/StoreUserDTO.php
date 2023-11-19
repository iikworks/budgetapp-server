<?php

namespace App\DTO\User;

class StoreUserDTO
{
    public string $firstName;
    public string $phoneNumber;
    public string $password;

    public function toArray(): array
    {
        return [
            'first_name' => $this->firstName,
            'phone_number' => $this->phoneNumber,
            'password' => $this->password,
        ];
    }
}
