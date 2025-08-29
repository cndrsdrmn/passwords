<?php

declare(strict_types=1);

namespace Tests;

use Cndrsdrmn\Passwords\Contracts\OtpPasswordBroker as OtpBrokerContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

beforeEach(function (): void {
    $this->user = new Fakes\UserFake(1, 'john@example.com');

    Auth::provider('fake', fn (): Fakes\FakeUserProvider => new Fakes\FakeUserProvider([
        $this->user->getEmailForPasswordReset() => $this->user,
    ]));

    $this->app['config']->set('auth.providers.users.driver', 'fake');
});

it('sends reset link notification via facade broker', function ($config): void {
    $this->app['config']->set($config);

    Notification::fake();

    $response = Password::sendResetLink(['email' => $this->user->getEmailForPasswordReset()]);

    expect($response)->toBe(Password::RESET_LINK_SENT);
})->with('auth passwords users');

it('verifies token via facade broker then exists succeeds', function ($config): void {
    $this->app['config']->set($config);

    $token = Password::createToken($this->user);

    $status = Password::markVerified([
        'email' => $this->user->getEmailForPasswordReset(), 'token' => '000000', 'email_user' => $this->user,
    ]);

    expect($status)->toBe(OtpBrokerContract::UNVERIFIED_TOKEN);

    $status = Password::markVerified([
        'email' => $this->user->getEmailForPasswordReset(), 'token' => $token, 'email_user' => $this->user,
    ]);

    expect($status)->toBe(OtpBrokerContract::VERIFIED_TOKEN);
})->with('auth passwords users');

it('resets password via facade broker and invalidates token', function ($config): void {
    $this->app['config']->set($config);

    $token = Password::createToken($this->user);

    $status = Password::markVerified([
        'email' => $this->user->getEmailForPasswordReset(), 'token' => $token, 'email_user' => $this->user,
    ]);

    expect($status)->toBe(OtpBrokerContract::VERIFIED_TOKEN);

    $called = false;
    $result = Password::reset([
        'email' => $this->user->getEmailForPasswordReset(),
        'token' => $token,
        'password' => 'new-secret',
        'email_user' => $this->user,
    ], function ($u, $password) use (&$called): void {
        $called = $password === 'new-secret';
    });

    expect($result)
        ->toBe(Password::PASSWORD_RESET)
        ->and($called)->toBeTrue();

    expect(Password::tokenExists($this->user, $token))->toBeFalse();
})->with('auth passwords users');
