<?php

namespace App\DTO\Auth;

use App\Models\User;

class SignInResultDTO
{
    public string $token;

    public User $user;
}
