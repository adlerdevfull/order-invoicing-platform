<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Domain\Order\Entities\Order;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Order $this->resource */
        return [
            'id' => $this->resource->id,
            'user_id' => $this->resource->userId,
            'status' => $this->resource->status->value,
            'subtotal' => $this->resource->subtotal?->toFloat(),
            'tax' => $this->resource->tax?->toFloat(),
            'shipping' => $this->resource->shipping?->toFloat(),
            'discount' => $this->resource->discount?->toFloat(),
            'total' => $this->resource->total?->toFloat(),
            'items' => array_map(fn ($item) => [
                'product_id' => $item->productId,
                'quantity' => $item->quantity,
                'unit_price' => $item->unitPrice->toFloat(),
                'total' => $item->total()->toFloat(),
            ], $this->resource->items()),
        ];
    }
}
