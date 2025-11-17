<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Domain\Product\Entities\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        /** @var Product $this->resource */
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'sku' => $this->resource->sku,
            'price_cents' => $this->resource->priceInCents,
            'price' => number_format($this->resource->priceInCents / 100, 2),
            'stock' => $this->resource->stock,
            'description' => $this->resource->description,
        ];
    }
}
