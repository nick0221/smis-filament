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
        Schema::create('student_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade'); // link to enrollment
            $table->decimal('amount', 10, 2); // payment amount
            $table->string('payment_method')->nullable(); // e.g., cash, gcash, bank transfer
            $table->string('reference_number')->nullable(); // receipt or transaction number
            $table->date('payment_date')->default(now('Asia/Manila')); // when payment was made
            $table->text('notes')->nullable(); // optional
            $table->string('status')->default('pending'); // pending, paid, failed
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->decimal('cash_tendered', 10, 2)->default(0)->nullable();
            $table->decimal('change', 10, 2)->default(0)->nullable();
            $table->foreignId('school_expense_id')->constrained()->onDelete('cascade')->nullable();
            $table->decimal('pay_amount', 10, 2)->default(0)->nullable();
            $table->string('gcash_reference_number')->nullable();
            $table->string('bank_reference_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('other_reference_number')->nullable();
            $table->string('other_notes')->nullable();
            $table->string('gcash_pay_amount')->default(0)->nullable();
            $table->string('bank_pay_amount')->default(0)->nullable();
            $table->string('other_pay_amount')->default(0)->nullable();

            $table->softDeletes();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_payments');
    }
};
