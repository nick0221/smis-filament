<?php

namespace Database\Seeders;

use App\Models\GradeLevel;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GradeLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            GradeLevel::create([
                'grade_name' => "Grade $i",

            ]);
        }
    }
}
