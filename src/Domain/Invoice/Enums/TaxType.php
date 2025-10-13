<?php

declare(strict_types=1);

namespace Domain\Invoice\Enums;

enum TaxType: string
{
    case General = 'general';       // 21%
    case Reduced = 'reduced';       // 10%
    case SuperReduced = 'super_reduced'; // 4%

    public function rate(): float
    {
        return match ($this) {
            self::General => 0.21,
            self::Reduced => 0.10,
            self::SuperReduced => 0.04,
        };
    }
}
