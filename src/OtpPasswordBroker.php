<?php

declare(strict_types=1);

namespace Cndrsdrmn\Passwords;

use Cndrsdrmn\Passwords\Contracts\OtpPasswordBroker as OtpPasswordBrokerContract;
use Illuminate\Auth\Passwords\PasswordBroker as BasePasswordBroker;
use SensitiveParameter;
use Throwable;

final class OtpPasswordBroker extends BasePasswordBroker implements OtpPasswordBrokerContract
{
    /**
     * The token repository instance.
     *
     * @var Contracts\OtpTokenRepositoryInterface
     */
    protected $tokens;

    /**
     * Mark a password resets token as verified.
     *
     * @param  array{email: string, token: string}  $credentials
     *
     * @throws Throwable
     */
    public function markVerified(#[SensitiveParameter] array $credentials): string
    {
        if (is_null($user = $this->getUser($credentials))) {
            return self::INVALID_USER;
        }

        if ($this->tokens->markVerified($user, $credentials['token'])) {
            return self::VERIFIED_TOKEN;
        }

        return self::UNVERIFIED_TOKEN;
    }
}
