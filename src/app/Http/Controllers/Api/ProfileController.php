<?php

namespace App\Http\Controllers\Api;

use App\Actions\Auth\GetAuthenticatedUserAction;
use App\DTO\User\UpdateUserDTO;
use App\Exceptions\Auth\UnauthorizedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;

class ProfileController extends Controller
{
    public function __construct(
        private readonly GetAuthenticatedUserAction $getAuthenticatedUserAction,
        private readonly UserService $userService,
    ) {
    }

    /**
     * Returns JSON representation of authenticated user.
     *
     * @throws UnauthorizedException If the user is not authenticated.
     */
    public function profile(): UserResource
    {
        return new UserResource(
            ($this->getAuthenticatedUserAction)(),
        );
    }

    /**
     * Updates an authenticated user.
     *
     * @throws UnauthorizedException If the user is not authenticated.
     */
    public function update(UpdateProfileRequest $request): UserResource
    {
        $dto = new UpdateUserDTO();
        $dto->phoneNumber = $request->phone_number;
        $dto->firstName = $request->first_name;
        $dto->password = $request->password;

        return new UserResource(
            $this->userService->update(
                user: ($this->getAuthenticatedUserAction)(),
                dto: $dto,
            ),
        );
    }
}
