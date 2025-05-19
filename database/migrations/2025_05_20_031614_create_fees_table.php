<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_expense_id')->constrained()->onDelete('cascade');
            $table->string('fee_name'); // e.g., Tuition, Exam Fee
            $table->decimal('fee_amount', 10, 2);
            $table->enum('fee_type', ['mandatory', 'optional'])->default('mandatory'); // Type of fee
            $table->foreignId('fee_category_id')->constrained()->onDelete('cascade'); // e.g., Academic, Administrative
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
