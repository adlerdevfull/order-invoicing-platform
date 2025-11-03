<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Models;

use Domain\Invoice\Enums\TaxType;
use Illuminate\Database\Eloquent\Model;

class InvoiceModel extends Model
{
    protected $table = 'invoices';
    protected $fillable = [
        'order_id', 'number', 'identification_key', 'tax_type',
        'net_amount', 'tax_amount', 'total_amount', 'digital_signature', 'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'tax_type' => TaxType::class,
            'issued_at' => 'immutable_datetime',
        ];
    }
}
