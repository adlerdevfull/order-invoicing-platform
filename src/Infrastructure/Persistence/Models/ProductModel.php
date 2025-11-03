<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductModel extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $fillable = ['name', 'sku', 'price_cents', 'stock', 'description'];

    public function stockLogs()
    {
        return $this->hasMany(StockLogModel::class, 'product_id');
    }
}
