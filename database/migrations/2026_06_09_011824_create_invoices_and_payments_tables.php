<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Invoices Table [cite: 379]
        Schema::create('invoices', function (Blueprint $table) { // [cite: 380]
            $table->id(); // [cite: 381]
            $table->string('invoice_number')->unique(); // ទម្រង់ INV-YYYY-XXXX [cite: 386, 585]
            $table->foreignId('sales_order_id')->constrained(); // [cite: 387]
            $table->foreignId('customer_id')->constrained(); // [cite: 388]
            $table->date('invoice_date'); // [cite: 389]
            $table->date('due_date'); // [cite: 390]
            $table->decimal('total_amount', 15, 2); // [cite: 391]
            $table->decimal('paid_amount', 15, 2)->default(0); // ប្រាក់បានបង់ [cite: 392]
            $table->decimal('outstanding_balance', 15, 2); // ប្រាក់នៅជំពាក់ [cite: 393]
            $table->enum('payment_status', ['unpaid', 'partial', 'paid'])->default('unpaid'); // [cite: 394]
            $table->timestamps(); // [cite: 395]
        });

        // 2. Payments Table [cite: 396]
        Schema::create('payments', function (Blueprint $table) { // [cite: 397]
            $table->id(); // [cite: 399]
            $table->foreignId('invoice_id')->constrained(); // [cite: 400]
            $table->decimal('amount', 15, 2); // ចំនួនទឹកប្រាក់បង់ [cite: 401]
            $table->date('payment_date'); // [cite: 402]
            $table->enum('method', ['cash', 'bank_transfer', 'cheque', 'online']); // វិធីសាស្ត្រទូទាត់ [cite: 403]
            $table->string('reference')->nullable(); // លេខយោង [cite: 404]
            $table->text('notes')->nullable(); // [cite: 405]
            $table->timestamps(); // [cite: 406]
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};
