<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class StockLogModel extends Model
{
    protected $table = 'stock_logs';
    protected $fillable = ['product_id', 'quantity_change', 'reason', 'user_id'];
}
