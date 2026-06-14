<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Supplier extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address'];

    // Relationship: Many-to-Many ទៅកាន់ Product [cite: 131]
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Product::class, 'product_supplier') // ដាក់ App\Models\ ពីមុខ [cite: 134]
            ->withPivot('cost_price') // [cite: 135]
            ->withTimestamps(); // [cite: 136]
    }
}
