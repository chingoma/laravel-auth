<?php

namespace Lockminds\LaravelAuth\Helpers;

use Illuminate\Validation\Rules\Password;

class Validations
{
    public static function changePassword(): array
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols(),
            ],
        ];
    }

    public static function resetPassword(): array
    {
        return [
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols(),
            ],
        ];
    }
}
