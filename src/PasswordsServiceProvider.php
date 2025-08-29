<?php

declare(strict_types=1);

namespace Cndrsdrmn\Passwords;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
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
        $this->configureResetPasswordToMail();
        $this->loadTranslationsFrom(__DIR__.'/../resource/lang', 'passwords');
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
     * Configure the reset password notification to use the mail channel.
     */
    private function configureResetPasswordToMail(): void
    {
        ResetPassword::toMailUsing(fn ($notifiable, $token) => (new MailMessage)
            ->subject(Lang::get('passwords::passwords.mail.subject'))
            ->line(Lang::get('passwords::passwords.mail.intro'))
            ->line(Lang::get('passwords::passwords.mail.instruction'))
            ->line($token)
            ->line(Lang::get('passwords::passwords.mail.expire', [
                'count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
            ]))
            ->line(Lang::get('passwords::passwords.mail.outro'))
        );
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
