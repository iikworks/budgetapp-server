<?php

namespace App\Http\Controllers\Api;

use App\Actions\Auth\GetAuthenticatedUserAction;
use App\Exceptions\Auth\UnauthorizedException;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function profile(GetAuthenticatedUserAction $getAuthenticatedUserAction): UserResource
    {
        try {
            return new UserResource(
                $getAuthenticatedUserAction(),
            );
        } catch (UnauthorizedException $e) {
            abort(401, $e->getMessage());
        }
    }
}
