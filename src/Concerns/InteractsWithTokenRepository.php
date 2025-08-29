<?php

declare(strict_types=1);

namespace Cndrsdrmn\Passwords\Concerns;

use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Override;

trait InteractsWithTokenRepository
{
    /**
     * Get password resetter record by given user and verified args.
     *
     * @return array{email: string, is_verified: bool, token: string, created_at: string}
     */
    abstract protected function getRecord(CanResetPasswordContract $user, bool $verified): array;

    /**
     * Create a new token for the user.
     */
    public function createNewToken(): string
    {
        return mb_str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Determine if a token record exists and is verified.
     *
     * @param  string  $token
     */
    #[Override]
    public function exists(CanResetPasswordContract $user, $token): bool
    {
        $record = $this->getRecord($user, true);

        return $this->shouldVerified($record, $token);
    }

    /**
     * Determine if a token record is unverified.
     *
     * @param  array{created_at: string, token: string}  $record
     */
    private function shouldUnverified(array $record, string $token): bool
    {
        return ! $this->shouldVerified($record, $token);
    }

    /**
     * Determine if a token record is verified.
     *
     * @param  array{created_at: string, token: string}  $record
     */
    private function shouldVerified(array $record, string $token): bool
    {
        return $record &&
            ! $this->tokenExpired($record['created_at']) &&
            $this->hasher->check($token, $record['token']);
    }
}
