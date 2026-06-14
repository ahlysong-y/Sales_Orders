<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_supplier', function (Blueprint $table) { // [cite: 123]
            $table->id(); // [cite: 124]
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete(); // [cite: 125]
            $table->foreignId('product_id')->constrained()->cascadeOnDelete(); // [cite: 129]
            $table->decimal('cost_price', 15, 2)->default(0); // ថ្លៃដើមទិញពី Supplier នេះ [cite: 129]
            $table->timestamps(); // [cite: 130]
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_supplier');
    }
};
