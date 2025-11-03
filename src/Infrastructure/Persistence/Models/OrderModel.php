<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Models;

use Domain\Order\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $fillable = ['user_id', 'status', 'subtotal', 'tax', 'shipping', 'discount', 'total'];

    protected function casts(): array
    {
        return ['status' => OrderStatus::class];
    }

    public function items()
    {
        return $this->hasMany(OrderItemModel::class, 'order_id');
    }

    public function invoice()
    {
        return $this->hasOne(InvoiceModel::class, 'order_id');
    }
}
