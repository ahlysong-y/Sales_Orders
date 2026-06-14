<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ឈ្មោះអតិថិជន [cite: 26, 31]
            $table->string('email')->nullable()->unique(); // អ៊ីមែល [cite: 26, 32]
            $table->string('phone')->nullable(); // លេខទូរស័ព្ទ [cite: 26, 33]
            $table->text('address')->nullable(); // អាសយដ្ឋាន [cite: 26, 34]
            $table->decimal('credit_limit', 15, 2)->default(0); // ដែនកំណត់ឥណទាន [cite: 26, 35]
            $table->enum('status', ['active', 'inactive'])->default('active'); // Active / Inactive [cite: 26, 36]
            $table->timestamps(); // [cite: 37]
            $table->softDeletes(); // [cite: 38]
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
