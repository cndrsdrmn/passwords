<?php

declare(strict_types=1);

namespace Cndrsdrmn\Passwords\Tokens;

use Cndrsdrmn\Passwords\Concerns\InteractsWithTokenRepository;
use Cndrsdrmn\Passwords\Contracts\OtpTokenRepositoryInterface;
use Illuminate\Auth\Passwords\CacheTokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Carbon;
use Override;
use SensitiveParameter;

final class CacheOtpTokenRepository extends CacheTokenRepository implements OtpTokenRepositoryInterface
{
    use InteractsWithTokenRepository;

    /**
     * Determine the cache key for the given user.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return string
     */
    public function cacheKey(CanResetPasswordContract $user): string
    {
        return hash('sha256', $user->getEmailForPasswordReset());
    }

    /**
     * Create a new token with a 6-digit numeric code.
     */
    #[Override]
    public function create(CanResetPasswordContract $user): string
    {
        $this->delete($user);

        $token = $this->createNewToken();

        $this->cache->put(
            $this->cacheKey($user),
            [$this->hasher->make($token), Carbon::now()->format($this->format), false],
            $this->expires,
        );

        return $token;
    }

    /**
     * Mark the token for the given user as verified.
     */
    public function markVerified(CanResetPasswordContract $user, #[SensitiveParameter] string $token): bool
    {
        $record = $this->getRecord($user, false);

        if ($this->shouldUnverified($record, $token)) {
            return false;
        }

        $created = Carbon::createFromFormat($this->format, $record['created_at']);

        $elapsed = max(0, Carbon::now()->diffInSeconds($created));
        $remaining = max(1, $this->expires - $elapsed);

        return $this->cache->put(
            $this->cacheKey($user),
            [$record['token'], $record['created_at'], true],
            $remaining,
        );
    }

    /**
     * Get password resetter record by given user and verified args.
     *
     * @return array{email: string, is_verified: bool, token: string, created_at: string}
     */
    protected function getRecord(CanResetPasswordContract $user, bool $verified): array
    {
        $payload = $this->cache->get($this->cacheKey($user));

        if (! is_array($payload) || count($payload) < 2) {
            return [];
        }

        /** @var array{0: string, 1: string, 2?: bool} $payload */
        [$hash, $createdAt] = $payload;
        $isVerified = (bool) ($payload[2] ?? false);

        if ($verified !== $isVerified) {
            return [];
        }

        return [
            'email' => $user->getEmailForPasswordReset(),
            'token' => $hash,
            'created_at' => $createdAt,
            'is_verified' => $isVerified,
        ];
    }
}
