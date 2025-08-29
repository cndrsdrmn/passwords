<?php

declare(strict_types=1);

use Cndrsdrmn\Passwords\Tokens\CacheOtpTokenRepository;
use Cndrsdrmn\Passwords\Tokens\DatabaseOtpTokenRepository;

dataset('auth passwords users', [
    'database' => fn (): array => [
        [
            'auth.passwords.users.driver' => 'database',
        ],
        DatabaseOtpTokenRepository::class,
    ],
    'cache' => fn (): array => [
        [
            'auth.passwords.users.driver' => 'cache',
            'cache.default' => 'array',
        ],
        CacheOtpTokenRepository::class,
    ],
]);
