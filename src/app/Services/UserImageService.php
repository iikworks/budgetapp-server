<?php

namespace App\Services;

use App\Models\User;

class UserImageService
{
    /**
     * Updates the user's image.
     *
     * @param  User  $user The user to update.
     * @param  ?string  $image The new image for the user. If null, the user's image will be cleared.
     * @return User The updated user.
     */
    public function update(User $user, ?string $image): User
    {
        $user->update([
            'image' => $image,
        ]);

        return $user;
    }
}
