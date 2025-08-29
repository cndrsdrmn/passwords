<?php

declare(strict_types=1);

namespace Tests\Fakes;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Notifications\Notifiable;

final class UserFake implements Authenticatable, CanResetPassword
{
    use Notifiable;

    public function __construct(private int $id, private string $email)
    {
        //
    }

    public function getAuthIdentifier(): int
    {
        return $this->id;
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthPassword(): string
    {
        return 'secret';
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getEmailForPasswordReset(): string
    {
        return $this->email;
    }

    public function getKey(): int
    {
        return $this->id;
    }

    public function getRememberToken(): string
    {
        return 'token';
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPassword($token));
    }

    public function setRememberToken($value): void
    {
        //
    }
}
