<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Domain\Product\Entities\Product;
use Domain\Product\Repositories\ProductRepositoryInterface;
use Infrastructure\Persistence\Models\ProductModel;

final class EloquentProductRepository implements ProductRepositoryInterface
{
    public function findById(int $id): ?Product
    {
        $model = ProductModel::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    public function findBySku(string $sku): ?Product
    {
        $model = ProductModel::where('sku', $sku)->first();
        return $model ? $this->toDomain($model) : null;
    }

    public function paginate(int $page = 1, int $perPage = 15, array $filters = []): array
    {
        $query = ProductModel::query();

        if (isset($filters['name'])) {
            $query->where('name', 'ilike', "%{$filters['name']}%");
        }
        if (isset($filters['min_stock'])) {
            $query->where('stock', '>=', $filters['min_stock']);
        }

        return $query->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->orderBy($filters['sort'] ?? 'id', $filters['direction'] ?? 'asc')
            ->get()
            ->map(fn ($m) => $this->toDomain($m))
            ->all();
    }

    public function count(array $filters = []): int
    {
        $query = ProductModel::query();
        if (isset($filters['name'])) {
            $query->where('name', 'ilike', "%{$filters['name']}%");
        }
        return $query->count();
    }

    public function save(Product $product): Product
    {
        $model = $product->id
            ? ProductModel::findOrFail($product->id)
            : new ProductModel();

        $model->fill([
            'name' => $product->name,
            'sku' => $product->sku,
            'price_cents' => $product->priceInCents,
            'stock' => $product->stock,
            'description' => $product->description,
        ]);
        $model->save();

        $product->id = $model->id;
        return $product;
    }

    public function delete(int $id): void
    {
        ProductModel::destroy($id);
    }

    private function toDomain(ProductModel $model): Product
    {
        return new Product(
            id: $model->id,
            name: $model->name,
            sku: $model->sku,
            priceInCents: $model->price_cents,
            stock: $model->stock,
            description: $model->description ?? '',
        );
    }
}
