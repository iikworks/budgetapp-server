<?php

namespace Tests\Feature\Actions\Auth;

use App\Actions\Auth\GetAuthenticatedUserAction;
use App\Exceptions\Auth\UnauthorizedException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class GetAuthenticatedUserActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tests that the action successfully gets the authenticated user.
     *
     * @throws UnauthorizedException If the user is not authenticated
     */
    public function test_successfully_gets_authenticated_user(): void
    {
        $user = User::factory()->create()->fresh();
        Auth::login($user);
        $action = $this->app->make(GetAuthenticatedUserAction::class);

        $this->assertEquals($user, $action());
    }

    /**
     * Tests that the action throws an unauthorized exception if the user is not authenticated.
     */
    public function test_throws_unauthorized_exception(): void
    {
        $action = $this->app->make(GetAuthenticatedUserAction::class);
        $this->expectException(UnauthorizedException::class);
        $action();
    }
}
