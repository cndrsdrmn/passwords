<?php

declare(strict_types=1);

namespace Tests\Fakes;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

final class FakeUserProvider implements UserProvider
{
    /**
     * @param  array<string, Authenticatable>  $byEmail
     */
    public function __construct(private array $byEmail = []) {}

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false): void
    {
        //
    }

    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        if (isset($credentials['email_user']) && $credentials['email_user'] instanceof Authenticatable) {
            return $credentials['email_user'];
        }

        $email = $credentials['email'] ?? null;

        if (is_string($email) && isset($this->byEmail[$email])) {
            return $this->byEmail[$email];
        }

        return null;
    }

    public function retrieveById($identifier): ?Authenticatable
    {
        foreach ($this->byEmail as $user) {
            if (method_exists($user, 'getAuthIdentifier') && $user->getAuthIdentifier() === $identifier) {
                return $user;
            }
        }

        return null;
    }

    public function retrieveByToken($identifier, $token): ?Authenticatable
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token): void
    {
        //
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return true;
    }
}
