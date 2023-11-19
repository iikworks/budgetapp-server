<?php

use App\Actions\Auth\GetAuthenticatedUserAction;
use App\Exceptions\Auth\UnauthorizedException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

test('successfully gets authenticated user', function () {
    $user = User::factory()->create()->fresh();
    Auth::login($user);
    $action = $this->app->make(GetAuthenticatedUserAction::class);

    expect($action())->toEqual($user);
});

test('throws unauthorized exception', function () {
    $action = $this->app->make(GetAuthenticatedUserAction::class);
    $this->expectException(UnauthorizedException::class);
    $action();
});
