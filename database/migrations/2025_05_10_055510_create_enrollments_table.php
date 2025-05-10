<?php

use App\Models\Section;
use App\Models\Student;
use App\Models\GradeLevel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Student::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(GradeLevel::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Section::class)->nullable()->constrained()->nullOnDelete();
            $table->year('school_year_from');
            $table->year('school_year_to');
            $table->enum('status', ['pending', 'enrolled', 'withdrawn', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
