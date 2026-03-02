<?php

namespace App\Enums;

enum OrderStatus: string
{
    case NEW        = 'new';
    case ASSIGNED   = 'assigned';
    case DELIVERING = 'delivering';
    case DELIVERED  = 'delivered';
    case CANCELLED  = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function canTransitionTo(OrderStatus $next): bool
    {
        return match ($this) {
            self::NEW        => in_array($next, [self::ASSIGNED, self::CANCELLED]),
            self::ASSIGNED   => $next === self::DELIVERING,
            self::DELIVERING => $next === self::DELIVERED,
            default          => $next === self::CANCELLED,
        };
    }
}
