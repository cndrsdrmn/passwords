# Laravel Auth Passwords - OTP Password Reset

A streamlined OTP password reset for your Laravel application. This package seamlessly integrates with the Password facade, so you can add a 6-digit OTP and a crucial verification step, managed by a new markVerified method, without changing your existing code. It just works.

## Installation

You can install the package via Composer:

```shell
composer require cndrsdrmn/passwords
```

The service provider is auto-discovered. If you have disabled auto-discovery, you'll need to manually register it in your `bootstrap/providers.php`:

```php
return [
    // ...
    Cndrsdrmn\Passwords\PasswordsServiceProvider::class,
],
```

After installation, run the migrations to create the necessary table:

```shell
php artisan migrate
```

You can optionally publish the migrations and translations if you need to customize them:

```shell
php artisan vendor:publish --tag=passwords-migrations
php artisan vendor:publish --tag=passwords-lang
```

## Configuration

This package uses the standard Laravel password configuration. You can find more details about configuring the password broker in the official [Laravel documentation](https://laravel.com/docs/12.x/passwords#configuration).

## Quickstart

This package extends Laravel's default password broker by introducing a `markVerified` method. This adds an essential validation step before a password can be reset.


Before the user can reset their password, you must verify the OTP they provide. The package adds a new `markVerified` method to the broker, which you can call directly from the facade.

```php
use Cndrsdrmn\Passwords\Contracts\OtpPasswordBroker as OtpBrokerContract;
use Illuminate\Support\Facades\Password;

$status = Password::markVerified([
    'email' => $request->email,
    'token' => $request->input('otp'),
]);

if ($status !== OtpBrokerContract::VERIFIED_TOKEN) {
    return back()->withErrors(['token' => __($status)]);
}
```

For the rest of the password reset flow, you can follow the instructions in the official [Laravel documentation](https://laravel.com/docs/12.x/passwords#routing).

## License

This package is open-sourced software licensed under The MIT License (MIT). See the [LICENSE](./LICENSE) file for more details.

## Credits

- Built by: Candra Sudirman
- Inspired by: This package is inspired by the secure and flexible design of Laravel's core authentication and password reset systems.
- Package Template: [Skeleton PHP](https://github.com/nunomaduro/skeleton-php)