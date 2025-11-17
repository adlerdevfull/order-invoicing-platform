<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Domain\Invoice\Entities\Invoice;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Invoice $this->resource */
        return [
            'id' => $this->resource->id,
            'order_id' => $this->resource->orderId,
            'number' => $this->resource->number,
            'identification_key' => $this->resource->identificationKey,
            'tax_type' => $this->resource->taxType->value,
            'tax_rate' => $this->resource->taxType->rate() * 100 . '%',
            'net_amount' => $this->resource->netAmount->toFloat(),
            'tax_amount' => $this->resource->taxAmount->toFloat(),
            'total_amount' => $this->resource->totalAmount->toFloat(),
            'digital_signature' => $this->resource->digitalSignature,
            'issued_at' => $this->resource->issuedAt?->format('Y-m-d\TH:i:s'),
        ];
    }
}
