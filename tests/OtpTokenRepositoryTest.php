<?php

declare(strict_types=1);

namespace Tests;

use Cndrsdrmn\Passwords\Contracts\OtpTokenRepositoryInterface;
use Cndrsdrmn\Passwords\OtpPasswordBrokerManager;
use Illuminate\Contracts\Auth\CanResetPassword;
use Mockery;

it('verifies token using database repository and then exists returns true', function ($config, $class): void {
    $this->app['config']->set($config);

    $manager = new OtpPasswordBrokerManager($this->app);

    /** @var OtpTokenRepositoryInterface $repo */
    $repo = (fn (): OtpTokenRepositoryInterface => $this->createTokenRepository($this->getConfig('users')))->call($manager);

    expect($repo)->toBeInstanceOf($class);

    $user = Mockery::mock(CanResetPassword::class, [
        'getEmailForPasswordReset' => 'dbuser@example.com',
    ]);

    $token = $repo->create($user);

    expect($token)
        ->toBeString()
        ->toHaveLength(6)
        ->and(ctype_digit($token))->toBeTrue();

    expect($repo)
        ->exists($user, $token)->toBeFalse()
        ->markVerified($user, '111111')->toBeFalse()
        ->markVerified($user, $token)->toBeTrue()
        ->exists($user, $token)->toBeTrue()
        ->markVerified($user, $token)->toBeFalse();
})->with('auth passwords users');

it('handles malformed cache payload gracefully', function (): void {
    $this->app['config']->set('auth.passwords.users.driver', 'cache');
    $this->app['config']->set('cache.default', 'array');

    $manager = new OtpPasswordBrokerManager($this->app);
    $repo = (fn (): OtpTokenRepositoryInterface => $this->createTokenRepository($this->getConfig('users')))->call($manager);

    $user = Mockery::mock(CanResetPassword::class, [
        'getEmailForPasswordReset' => 'edge@example.com',
    ]);

    $key = (fn () => $repo->cacheKey($user))->call($repo);

    $this->app['cache']->store()->put($key, ['only one'], 60);

    expect($repo->exists($user, '123456'))->toBeFalse();
});
