<?php

declare(strict_types=1);

namespace Tests;

use Cndrsdrmn\Passwords\Contracts\OtpTokenRepositoryInterface;
use Cndrsdrmn\Passwords\OtpPasswordBrokerManager;
use InvalidArgumentException;

beforeEach(function (): void {
    $this->manager = new OtpPasswordBrokerManager($this->app);
});

test('create token repository', function (string $driver): void {
    $this->app['config']->set(['auth.passwords.users.driver' => $driver]);

    $config = $this->app['config']->get('auth.passwords.users');

    $repository = (fn () => $this->createTokenRepository($config));

    expect($repository->call($this->manager))->toBeInstanceOf(OtpTokenRepositoryInterface::class);
})->with(['cache', 'database']);

test('throw an exception with invalid broker config', function (): void {
    $broker = fn () => $this->manager->broker('invalid');

    expect($broker)->toThrow(InvalidArgumentException::class, 'Password resetter [invalid] is not defined.');
});
