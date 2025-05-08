<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Department;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\FacultyStaff;
use Illuminate\Database\Seeder;
use Database\Seeders\DesignationSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DesignationSeeder::class,
        ]);

        // User::factory(10)->create();
        Student::factory(30)->create();
        Department::factory()->count(10)->create();
        FacultyStaff::factory(10)->create();

        User::factory()->create([
            'name' => 'Default User',
            'email' => 'default@admin.com',
        ]);
    }
}
