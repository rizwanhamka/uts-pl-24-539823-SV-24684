<?php

namespace App\Support;

use Illuminate\Contracts\Auth\Authenticatable;

class Actor
{
    /** Penanda pelaku untuk log: hanya ID internal, tanpa email atau nama. */
    public static function label(?Authenticatable $user): string
    {
        return $user ? 'user:'.$user->getAuthIdentifier() : 'anonymous';
    }
}
