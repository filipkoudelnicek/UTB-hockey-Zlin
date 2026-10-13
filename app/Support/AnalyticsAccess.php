<?php

namespace App\Support;

class AnalyticsAccess
{
    public const PERMISSION = 'analytics.view';

    public static function allowed(): bool
    {
        return auth()->user()?->hasPermission(self::PERMISSION) ?? false;
    }
}
