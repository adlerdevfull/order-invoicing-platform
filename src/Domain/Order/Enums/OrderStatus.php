<?php

declare(strict_types=1);

namespace Domain\Order\Enums;

enum OrderStatus: string
{
    case Draft = 'draft';
    case Confirmed = 'confirmed';
    case Paid = 'paid';
    case Shipped = 'shipped';
    case Cancelled = 'cancelled';

    public function canTransitionTo(self $next): bool
    {
        return match ($this) {
            self::Draft => in_array($next, [self::Confirmed, self::Cancelled]),
            self::Confirmed => in_array($next, [self::Paid, self::Cancelled]),
            self::Paid => in_array($next, [self::Shipped, self::Cancelled]),
            self::Shipped, self::Cancelled => false,
        };
    }
}
