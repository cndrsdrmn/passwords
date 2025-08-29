<?php

declare(strict_types=1);

namespace Cndrsdrmn\Passwords;

use Cndrsdrmn\Passwords\Contracts\OtpPasswordBroker as OtpPasswordBrokerContract;
use Cndrsdrmn\Passwords\Contracts\OtpTokenRepositoryInterface;
use Cndrsdrmn\Passwords\Tokens\CacheOtpTokenRepository;
use Cndrsdrmn\Passwords\Tokens\DatabaseOtpTokenRepository;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use InvalidArgumentException;
use Override;

final class OtpPasswordBrokerManager extends PasswordBrokerManager
{
    /**
     * Create a token repository instance based on the given configuration.
     *
     * @param  array<string, mixed>  $config
     */
    #[Override]
    protected function createTokenRepository(array $config): OtpTokenRepositoryInterface
    {
        $key = $this->app['config']['app.key'];

        if (str_starts_with((string) $key, 'base64:')) {
            $key = base64_decode(mb_substr((string) $key, 7));
        }

        if (isset($config['driver']) && $config['driver'] === 'cache') {
            return new CacheOtpTokenRepository(
                cache: $this->app['cache']->store($config['store'] ?? null),
                hasher: $this->app['hash'],
                hashKey: $key,
                expires: ($config['expire'] ?? 60) * 60,
                throttle: $config['throttle'] ?? 0,
            );
        }

        return new DatabaseOtpTokenRepository(
            connection: $this->app['db']->connection($config['connection'] ?? null),
            hasher: $this->app['hash'],
            table: $config['table'],
            hashKey: $key,
            expires: ($config['expire'] ?? 60) * 60,
            throttle: $config['throttle'] ?? 0,
        );
    }

    /**
     * {@inheritDoc}
     */
    #[Override]
    protected function resolve($name): OtpPasswordBrokerContract
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Password resetter [{$name}] is not defined.");
        }

        return new OtpPasswordBroker(
            tokens: $this->createTokenRepository($config),
            users: $this->app['auth']->createUserProvider($config['provider'] ?? null),
            dispatcher: $this->app['events'] ?? null,
            timeboxDuration: $this->app['config']->get('auth.timebox_duration', 200000),
        );
    }
}
