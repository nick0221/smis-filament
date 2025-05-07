<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $designations = [
            'Teacher',
            'Principal',
            'Vice Principal',
            'Librarian',
            'Registrar',
            'Guidance Counselor',
            'Administrative Staff',
        ];

        foreach ($designations as $title) {
            Designation::firstOrCreate(['title' => $title]);
        }
    }
}
