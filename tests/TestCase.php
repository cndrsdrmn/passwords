<?php

declare(strict_types=1);

namespace Tests;

use Cndrsdrmn\Passwords\PasswordsServiceProvider;
use Illuminate\Auth\Passwords\PasswordResetServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

abstract class TestCase extends TestbenchTestCase
{
    /**
     * Define database migrations for Testbench.
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

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
