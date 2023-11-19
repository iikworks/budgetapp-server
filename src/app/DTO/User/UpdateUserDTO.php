<?php

namespace App\DTO\User;

class UpdateUserDTO
{
    public ?string $firstName;
    public ?string $phoneNumber;
    public ?string $password;

    public function toArray(): array
    {
        $data = [];
        if ($this->firstName) {
            $data['first_name'] = $this->firstName;
        }
        if ($this->phoneNumber) {
            $data['phone_number'] = $this->phoneNumber;
        }
        if ($this->password) {
            $data['password'] = $this->password;
        }

        return $data;
    }
}
