<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. SO Header Table
        Schema::create('sales_orders', function (Blueprint $table) { // [cite: 254, 255]
            $table->id(); // [cite: 256]
            $table->string('so_number')->unique(); // ទម្រង់ SO-YYYY-XXXX [cite: 257, 585]
            $table->foreignId('customer_id')->constrained(); // [cite: 258]
            $table->foreignId('created_by')->constrained('users'); // [cite: 259]
            $table->date('order_date'); // [cite: 260]
            $table->date('delivery_date')->nullable(); // [cite: 261]
            $table->enum('status', ['draft', 'confirmed', 'delivered', 'cancelled'])->default('draft'); // [cite: 262, 263]
            $table->decimal('subtotal', 15, 2)->default(0); // [cite: 264]
            $table->decimal('discount_amount', 15, 2)->default(0); // [cite: 265]
            $table->decimal('tax_amount', 15, 2)->default(0); // [cite: 266]
            $table->decimal('total_amount', 15, 2)->default(0); // [cite: 267]
            $table->text('notes')->nullable(); // [cite: 271]
            $table->timestamps(); // [cite: 272]
            $table->softDeletes(); // [cite: 273]
        });

        // 2. SO Lines Table
        Schema::create('sales_order_items', function (Blueprint $table) { // [cite: 275, 276]
            $table->id(); // [cite: 278]
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete(); // [cite: 279]
            $table->foreignId('product_id')->constrained(); // [cite: 280]
            $table->decimal('quantity', 10, 2); // [cite: 281]
            $table->decimal('unit_price', 15, 2); // [cite: 282]
            $table->decimal('discount_percent', 5, 2)->default(0); // ភាគរយបញ្ចុះតម្លៃ [cite: 283]
            $table->decimal('subtotal', 15, 2); // (quantity * unit_price) - discount [cite: 284]
            $table->timestamps(); // [cite: 285]
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_items');
        Schema::dropIfExists('sales_orders');
    }
};
