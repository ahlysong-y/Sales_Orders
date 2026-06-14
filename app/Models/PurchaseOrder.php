<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = ['po_number', 'supplier_id', 'created_by', 'order_date', 'expected_date', 'status', 'total_amount', 'notes'];

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
