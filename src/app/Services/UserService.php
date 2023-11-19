<?php

namespace App\Services;

use App\Dto\User\StoreUserDTO;
use App\Dto\User\UpdateUserDTO;
use App\Models\User;

class UserService
{
    /**
     * Stores a new user in the database.
     *
     * @param  StoreUserDTO  $dto The data transfer object containing the user information.
     * @return User The newly created User object.
     */
    public function create(StoreUserDTO $dto): User
    {
        return User::query()
            ->create($dto->toArray());
    }

    /**
     * Updates a user in the database with the provided data.
     *
     * @param  User  $user The user to update.
     * @param  UpdateUserDTO  $dto The data to update the user with.
     * @return User The updated user.
     */
    public function update(User $user, UpdateUserDTO $dto): User
    {
        $user->update($dto->toArray());

        return $user;
    }
}
