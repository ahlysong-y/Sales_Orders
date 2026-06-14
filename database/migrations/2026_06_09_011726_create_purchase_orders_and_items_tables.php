<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. PO Header Table 
        Schema::create('purchase_orders', function (Blueprint $table) { // [cite: 144]
            $table->id(); // [cite: 146]
            $table->string('po_number')->unique(); // ទម្រង់ PO-YYYY-XXXX [cite: 147, 585]
            $table->foreignId('supplier_id')->constrained(); // [cite: 148]
            $table->foreignId('created_by')->constrained('users'); // អ្នកបង្កើត [cite: 149]
            $table->date('order_date'); // [cite: 150]
            $table->date('expected_date')->nullable(); // [cite: 151]
            $table->enum('status', ['draft', 'sent', 'received', 'cancelled'])->default('draft'); // [cite: 152, 153]
            $table->decimal('total_amount', 15, 2)->default(0); // [cite: 154]
            $table->text('notes')->nullable(); // [cite: 155]
            $table->timestamps(); // [cite: 156]
            $table->softDeletes(); // [cite: 157]
        });

        // 2. PO Lines Table 
        Schema::create('purchase_order_items', function (Blueprint $table) { // [cite: 161, 162]
            $table->id(); // [cite: 163]
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete(); // [cite: 165]
            $table->foreignId('product_id')->constrained(); // [cite: 166]
            $table->decimal('quantity', 10, 2); // [cite: 167]
            $table->decimal('unit_price', 15, 2); // [cite: 168]
            // គណនា subtotal ស្វ័យប្រវត្តិចេញពី database (quantity * unit_price) [cite: 169]
            $table->decimal('subtotal', 15, 2)->storedAs('quantity * unit_price'); // [cite: 169]
            $table->timestamps(); // [cite: 170]
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
