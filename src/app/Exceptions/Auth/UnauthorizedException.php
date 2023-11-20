<?php

namespace App\Exceptions\Auth;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UnauthorizedException extends Exception
{
    public function render(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'Unauthorized.',
        ], Response::HTTP_UNAUTHORIZED);
    }
}
