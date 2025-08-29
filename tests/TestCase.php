<?php

declare(strict_types=1);

namespace Tests;

use Cndrsdrmn\Passwords\PasswordsServiceProvider;
use Illuminate\Auth\Passwords\PasswordResetServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

abstract class TestCase extends TestbenchTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     *
     * @api
     */
    protected function getPackageProviders($app): array
    {
        return [PasswordResetServiceProvider::class, PasswordsServiceProvider::class];
    }
}
