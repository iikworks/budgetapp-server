<?php

namespace App\Http\Controllers\Api\Auth;

use App\DTO\Auth\CreateSignInCredentialsDTOFactory;
use App\Exceptions\Auth\AuthenticateFailedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignInRequest;
use App\Http\Resources\Auth\SignInResultResource;
use App\Services\AuthenticateService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SignInController extends Controller
{
    public function __construct(
        private readonly AuthenticateService $service,
    ) {
    }

    /**
     * Handles the Authentication via credentials.
     *
     * @param  SignInRequest  $request The Sign In request object containing the credentials.
     * @return SignInResultResource|JsonResponse The Sign In result with a token
     * and a user resource or a JSON response with error.
     */
    public function handle(SignInRequest $request): SignInResultResource|JsonResponse
    {
        try {
            return new SignInResultResource(
                $this->service->authenticateViaCredentials(
                    CreateSignInCredentialsDTOFactory::fromRequest($request),
                ),
            );
        } catch (AuthenticateFailedException) {
            return response()->json([
                'message' => 'Authentication failed.',
            ], Response::HTTP_UNAUTHORIZED);
        }
    }
}
