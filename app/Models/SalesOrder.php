<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrder extends Model
{
    protected $fillable = ['so_number', 'customer_id', 'created_by', 'order_date', 'delivery_date', 'status', 'subtotal', 'discount_amount', 'tax_amount', 'total_amount', 'notes'];

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
