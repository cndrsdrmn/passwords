<?php

declare(strict_types=1);

namespace Tests;

use Cndrsdrmn\Passwords\Contracts\OtpPasswordBroker as OtpPasswordBrokerContract;
use Cndrsdrmn\Passwords\Contracts\OtpTokenRepositoryInterface;
use Cndrsdrmn\Passwords\OtpPasswordBroker;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Auth\UserProvider;
use Mockery;

afterEach(function (): void {
    Mockery::close();
});

test('mark verified returns invalid user', function (): void {
    $users = Mockery::mock(UserProvider::class, ['retrieveByCredentials' => null]);
    $repository = Mockery::mock(OtpTokenRepositoryInterface::class);

    $broker = new OtpPasswordBroker($repository, $users, $this->app['events']);

    expect($broker->markVerified(['email' => 'user@example.com']))->toBe(PasswordBroker::INVALID_USER);
});

test('mark verified returns verified token', function (): void {
    $users = Mockery::mock(UserProvider::class, ['retrieveByCredentials' => Mockery::mock(CanResetPassword::class)]);
    $repository = Mockery::mock(OtpTokenRepositoryInterface::class, ['markVerified' => true]);

    $broker = new OtpPasswordBroker($repository, $users, $this->app['events']);

    expect($broker->markVerified(['email' => 'user@example.com', 'token' => 'token']))->toBe(OtpPasswordBrokerContract::VERIFIED_TOKEN);
});

test('mark verified returns unverified token', function (): void {
    $users = Mockery::mock(UserProvider::class, ['retrieveByCredentials' => Mockery::mock(CanResetPassword::class)]);
    $repository = Mockery::mock(OtpTokenRepositoryInterface::class, ['markVerified' => false]);

    $broker = new OtpPasswordBroker($repository, $users, $this->app['events']);

    expect($broker->markVerified(['email' => 'user@example.com', 'token' => 'token']))->toBe(OtpPasswordBrokerContract::UNVERIFIED_TOKEN);
});
