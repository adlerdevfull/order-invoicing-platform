<?php

declare(strict_types=1);

namespace Domain\Auth\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Seller = 'seller';
    case Financial = 'financial';
}
