<?php

use App\Models\Department;
use App\Models\User;
use App\Models\Designation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('faculty_staff', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('extension_name')->nullable(); // e.g. Jr., Sr.
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->foreignIdFor(Designation::class)->onDelete('set null')->nullable();
            $table->foreignIdFor(Department::class)->onDelete('set null')->nullable();
            $table->string('photo_path')->nullable();
            $table->foreignIdFor(User::class, 'created_by')->onDelete('cascade')->nullable();
            $table->foreignIdFor(User::class, 'last_updated_by')->onDelete('cascade')->nullable();
            $table->foreignIdFor(User::class)->onDelete('cascade')->nullable();
            $table->foreignIdFor(User::class, 'deleted_by')->onDelete('cascade')->nullable();
            $table->timestamps();
            $table->softDeletes(); // For archiving

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_staff');
    }
};
