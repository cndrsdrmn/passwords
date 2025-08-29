<?php

declare(strict_types=1);

namespace Cndrsdrmn\Passwords\Tokens;

use Cndrsdrmn\Passwords\Concerns\InteractsWithTokenRepository;
use Cndrsdrmn\Passwords\Contracts\OtpTokenRepositoryInterface;
use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use SensitiveParameter;

/**
 * Database-backed token repository that generates 6-digit numeric tokens.
 */
final class DatabaseOtpTokenRepository extends DatabaseTokenRepository implements OtpTokenRepositoryInterface
{
    use InteractsWithTokenRepository;

    /**
     * Mark a token of password resets is verified.
     */
    public function markVerified(CanResetPasswordContract $user, #[SensitiveParameter] string $token): bool
    {
        $record = $this->getRecord($user, false);

        if ($this->shouldUnverified($record, $token)) {
            return false;
        }

        return (bool) $this->getTable()->where([
            'email' => $user->getEmailForPasswordReset(),
        ])->update([
            'is_verified' => true,
        ]);
    }

    /**
     * Get password resetter record by given user and verified args.
     *
     * @return array{email: string, is_verified: bool, token: string, created_at: string}
     */
    protected function getRecord(CanResetPasswordContract $user, bool $verified): array
    {
        return (array) $this->getTable()->where([ // @phpstan-ignore return.type
            'email' => $user->getEmailForPasswordReset(),
            'is_verified' => $verified,
        ])->first();
    }
}
