<?php

use App\Models\ClassRoom;
use App\Models\Section;
use App\Models\Student;
use App\Models\GradeLevel;
use App\Models\User;
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
            $table->foreignIdFor(ClassRoom::class)->constrained()->nullOnDelete()->nullOnUpdate();
            $table->foreignIdFor(Section::class)->constrained()->nullOnDelete()->nullOnUpdate();
            $table->foreignIdFor(GradeLevel::class)->constrained()->nullOnDelete()->nullOnUpdate();
            $table->year('school_year_from');
            $table->year('school_year_to');
            $table->string('status_key')->nullable(); // FK to student_statuses.key
            $table->string('payment_status')->nullable(); // FK to student_statuses.key
            $table->string('initial_average_grade')->nullable();
            $table->string('reference_number')->nullable();
            $table->foreignIdFor(User::class, 'created_by')->nullable()->constrained()->nullOnDelete()->nullOnUpdate();
            $table->foreignIdFor(User::class, 'last_updated_by')->nullable()->constrained()->cascadeOnUpdate();
            $table->foreignIdFor(User::class, 'deleted_by')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();



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
