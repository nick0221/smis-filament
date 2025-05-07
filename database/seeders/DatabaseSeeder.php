<?php

namespace Database\Seeders;

use App\Models\FacultyStaff;
use App\Models\User;
use App\Models\Student;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        FacultyStaff::factory(10)->create();

        User::factory()->create([
            'name' => 'Default User',
            'email' => 'default@admin.com',
        ]);
    }
}
