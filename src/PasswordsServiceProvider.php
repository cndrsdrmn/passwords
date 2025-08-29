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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
