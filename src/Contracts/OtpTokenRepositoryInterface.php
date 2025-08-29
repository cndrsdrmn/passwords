<?php

declare(strict_types=1);

namespace Cndrsdrmn\Passwords\Contracts;

use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use SensitiveParameter;

interface OtpTokenRepositoryInterface extends TokenRepositoryInterface
{
    /**
     * Mark a token of password resets is verified.
     */
    public function markVerified(CanResetPasswordContract $user, #[SensitiveParameter] string $token): bool;
}
