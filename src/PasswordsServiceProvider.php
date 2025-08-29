<?php

declare(strict_types=1);

namespace Cndrsdrmn\Passwords;

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
        //
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
}
