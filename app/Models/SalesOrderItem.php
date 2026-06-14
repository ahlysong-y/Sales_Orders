<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderItem extends Model
{
    protected $fillable = ['sales_order_id', 'product_id', 'quantity', 'unit_price', 'discount_percent', 'subtotal'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
