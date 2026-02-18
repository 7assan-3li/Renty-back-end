<?php

namespace App\Constants;

class CarStatus
{
    public const AVAILABLE   = 'available';
    public const BOOKED      = 'booked';
    public const MAINTENANCE = 'maintenance';

    public static function all(): array
    {
        return [
            self::AVAILABLE,
            self::BOOKED,
            self::MAINTENANCE,
        ];
    }
}
