<?php

namespace App\Http\Controllers\Api\Auth;

use App\DTO\User\StoreUserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignUpRequest;
use App\Http\Resources\Auth\SignInResultResource;
use App\Services\AuthenticateService;
use App\Services\UserService;

class SignUpController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly AuthenticateService $authenticateService,
    )
    {
    }

    /**
     * Handles the user's registration.
     *
     * @param SignUpRequest $request The Sign-Up request object containing the user's data.
     * @return SignInResultResource The Sign In result with a token and a user resource.
     */
    public function handle(SignUpRequest $request): SignInResultResource
    {
        $dto = new StoreUserDTO();
        $dto->firstName = $request->first_name;
        $dto->phoneNumber = $request->phone_number;
        $dto->password = $request->password;

        return new SignInResultResource(
            $this->authenticateService->authenticateUser(
                $this->userService->create($dto),
            ),
        );
    }
}
