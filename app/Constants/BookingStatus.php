<?php

namespace App\Constants;

class BookingStatus
{
    public const PENDING = 'pending';
    public const CONFIRMED = 'confirmed';
    public const CANCELLED = 'cancelled';
    public const COMPLETED = 'completed';
    public const DONE = 'done'; // Added to match current service usage

    public static function all(): array
    {
        return [
            self::PENDING,
            self::CONFIRMED,
            self::CANCELLED,
            self::COMPLETED,
            self::DONE,
        ];
    }
}
