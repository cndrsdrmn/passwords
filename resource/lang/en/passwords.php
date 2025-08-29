<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | outcome such as failure due to an invalid password / reset token.
    |
    */

    'unverified' => 'The provided reset code is not verified.',
    'verified' => 'The reset code has been verified successfully.',

    /*
     |--------------------------------------------------------------------------
     | Mail Content Language Lines
     |--------------------------------------------------------------------------
     |
     | The following language lines are the default lines which match reasons
     | that are given by the password broker for a password update attempt
     | outcome such as failure due to an invalid password / reset token.
     |
     */
    'mail' => [
        'subject' => 'Your Password Reset Code',
        'intro' => 'You are receiving this email because we received a password reset request for your account.',
        'instruction' => 'Use the following code to reset your password:',
        'expire' => 'This code will expire in :count minutes.',
        'outro' => 'If you did not request a password reset, no further action is required.',
    ],
];
