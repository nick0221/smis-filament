<?php

use App\Models\FacultyStaff;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('class_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_name'); // e.g., "Grade 1 - Ruby"
            $table->string('room_number')->nullable();
            $table->foreignId('section_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('grade_level_id')->nullable()->constrained()->nullOnDelete();
            $table->string('average_grade_from')->nullable();
            $table->string('average_grade_to')->nullable();
            $table->string('criteria_description')->nullable();
            $table->foreignIdFor(FacultyStaff::class)->constrained()->onDelete('cascade')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_rooms');
    }
};
