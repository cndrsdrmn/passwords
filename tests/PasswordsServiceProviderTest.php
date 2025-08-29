<?php

declare(strict_types=1);

namespace Tests;

use Cndrsdrmn\Passwords\PasswordsServiceProvider;
use Illuminate\Support\Facades\Schema;

it('boots and loads dependencies', function (): void {
    expect(Schema::hasTable('password_reset_tokens'))->toBeTrue();

    $service = new PasswordsServiceProvider($this->app);
    $service->boot();
});

it('boots and loads dependencies with nonexistent table', function (): void {
    $this->app['config']->set('auth.defaults.passwords', 'alt');
    $this->app['config']->set('auth.passwords.alt', [
        'driver' => 'database',
        'table' => 'nonexistent_password_reset_tokens',
        'expire' => 60,
    ]);

    expect(Schema::hasTable('nonexistent_password_reset_tokens'))->toBeFalse();

    $service = new PasswordsServiceProvider($this->app);
    $service->boot();
});

it('registers and resolves dependencies', function (): void {
    $service = new PasswordsServiceProvider($this->app);
    $service->register();

    expect(true)->toBeTrue();
});
