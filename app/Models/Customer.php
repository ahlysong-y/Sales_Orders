<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'credit_limit',
        'status'
    ];

    // Relationship: Customer មាន SalesOrders ច្រើន [cite: 49]
    public function salesOrders(): HasMany
    {
        return $this->hasMany(\App\Models\SalesOrder::class);
    }

    // Relationship: Customer មាន Invoices ច្រើន [cite: 57]
    public function invoices(): HasMany
    {
        return $this->hasMany(\App\Models\Invoice::class);
    }
}
