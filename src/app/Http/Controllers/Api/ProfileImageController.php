<?php

namespace App\Http\Controllers\Api;

use App\Actions\Auth\GetAuthenticatedUserAction;
use App\Exceptions\Auth\UnauthorizedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileImageRequest;
use App\Http\Resources\UserResource;
use App\Services\ImageService;
use App\Services\UserImageService;

class ProfileImageController extends Controller
{
    public function __construct(
        private readonly GetAuthenticatedUserAction $getAuthenticatedUserAction,
        private readonly ImageService $imageService,
        private readonly UserImageService $userImageService,
    ) {
    }

    /**
     * Updates a user's profile image.
     *
     * @throws UnauthorizedException If the user is not authenticated.
     */
    public function update(UpdateProfileImageRequest $request): UserResource
    {
        return new UserResource(
            $this->userImageService->update(
                user: ($this->getAuthenticatedUserAction)(),
                image: $this->imageService->store(
                    path: ImageService::PROFILES_IMAGES_PATH,
                    image: $request->image,
                ),
            ),
        );
    }

    /**
     * Deletes a user's profile image.
     *
     * @throws UnauthorizedException If the user is not authenticated.
     */
    public function destroy(): UserResource
    {
        $user = ($this->getAuthenticatedUserAction)();

        $this->imageService->delete(
            ($this->getAuthenticatedUserAction)()->image,
        );

        return new UserResource(
            $this->userImageService->update(
                user: $user,
                image: null,
            ),
        );
    }
}
