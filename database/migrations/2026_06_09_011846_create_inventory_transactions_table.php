<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained(); // [cite: 238]
            $table->enum('type', ['stock_in', 'stock_out']); // ប្រភេទចលនា [cite: 241, 364]
            $table->decimal('quantity', 10, 2); // ចំនួន [cite: 242, 366]
            $table->string('reference_type'); // 'Purchase Order' ឬ 'Sales Order' [cite: 244, 367]
            $table->unsignedBigInteger('reference_id'); // PO ID ឬ SO ID [cite: 245, 368]
            $table->text('note')->nullable(); // [cite: 246]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
