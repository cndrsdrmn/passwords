<?php

declare(strict_types=1);

namespace Cndrsdrmn\Passwords;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Override;

final class PasswordsServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    #[Override]
    public function register(): void
    {
        $this->booted(function (): void {
            $this->configurePasswords();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureLoadMigrations();
    }

    /**
     * Configure the migrations.
     */
    private function configureLoadMigrations(): void
    {
        if (Schema::hasTable($this->getPasswordResetTokensTable())) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations/2025_08_29_000001_add_is_verified_to_password_reset_tokens_table.php');
        } else {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    /**
     * Configure the passwords.
     */
    private function configurePasswords(): void
    {
        if ($this->app->bound('auth.password')) {
            $this->app->extend('auth.password', fn (): OtpPasswordBrokerManager => new OtpPasswordBrokerManager($this->app));
        }

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'passwords-migrations');

            $this->publishes([
                __DIR__.'/../resource/lang' => base_path('lang/vendor/passwords'),
            ], 'passwords-lang');
        }
    }

    /**
     * Get the password reset tokens table.
     */
    private function getPasswordResetTokensTable(): string
    {
        $provider = $this->app['config']->get('auth.defaults.passwords', 'users');

        return $this->app['config']->get("auth.passwords.{$provider}.table", 'password_reset_tokens');
    }
}
