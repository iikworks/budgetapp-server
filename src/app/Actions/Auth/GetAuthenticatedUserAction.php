<?php

namespace App\Actions\Auth;

use App\Exceptions\Auth\UnauthorizedException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

readonly class GetAuthenticatedUserAction
{
    /**
     * Returns the current authenticated user.
     *
     * @return User The authenticated user.
     *
     * @throws UnauthorizedException If the user is not authenticated.
     */
    public function __invoke(): User
    {
        $user = Auth::user();
        if (! $user) {
            throw new UnauthorizedException('User is not authenticated.');
        }

        return $user;
    }
}
