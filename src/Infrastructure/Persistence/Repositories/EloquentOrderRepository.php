<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Repositories;

use Domain\Order\Entities\{Order, OrderItem};
use Domain\Order\Enums\OrderStatus;
use Domain\Order\Repositories\OrderRepositoryInterface;
use Domain\Order\ValueObjects\Money;
use Infrastructure\Persistence\Models\{OrderModel, OrderItemModel};
use Illuminate\Support\Facades\DB;

final class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function findById(int $id): ?Order
    {
        $model = OrderModel::with('items')->find($id);
        return $model ? $this->toDomain($model) : null;
    }

    public function paginate(int $page = 1, int $perPage = 15, array $filters = []): array
    {
        $query = OrderModel::with('items');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->orderBy($filters['sort'] ?? 'id', $filters['direction'] ?? 'desc')
            ->get()
            ->map(fn ($m) => $this->toDomain($m))
            ->all();
    }

    public function count(array $filters = []): int
    {
        $query = OrderModel::query();
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        return $query->count();
    }

    public function save(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $model = $order->id
                ? OrderModel::findOrFail($order->id)
                : new OrderModel();

            $model->fill([
                'user_id' => $order->userId,
                'status' => $order->status->value,
                'subtotal' => $order->subtotal?->amount ?? 0,
                'tax' => $order->tax?->amount ?? 0,
                'shipping' => $order->shipping?->amount ?? 0,
                'discount' => $order->discount?->amount ?? 0,
                'total' => $order->total?->amount ?? 0,
            ]);
            $model->save();
            $order->id = $model->id;

            if (!empty($order->items())) {
                $model->items()->delete();
                foreach ($order->items() as $item) {
                    $model->items()->create([
                        'product_id' => $item->productId,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unitPrice->amount,
                    ]);
                }
            }

            return $order;
        });
    }

    private function toDomain(OrderModel $model): Order
    {
        $order = new Order(
            id: $model->id,
            userId: $model->user_id,
            status: $model->status,
            subtotal: new Money($model->subtotal),
            tax: new Money($model->tax),
            shipping: new Money($model->shipping),
            discount: new Money($model->discount),
            total: new Money($model->total),
        );

        $items = $model->items->map(fn ($i) => new OrderItem(
            id: $i->id,
            productId: $i->product_id,
            quantity: $i->quantity,
            unitPrice: new Money($i->unit_price),
        ))->all();

        $order->setItems($items);
        return $order;
    }
}
