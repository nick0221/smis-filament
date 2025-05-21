<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('student_statuses')->insert([
           [
               'key' => 'pending',
               'label' => 'Pending',
               'color' => 'danger',
               'description' => 'Student has not been officially enrolled in the system.',
           ],
           [
               'key' => 'registered',
               'label' => 'Registered',
               'color' => 'primary',
               'description' => "Student has submitted basic information, but hasn't paid or been enrolled yet.",
           ],
           [
               'key' => 'unpaid',
               'label' => 'Unpaid',
               'color' => 'warning',
               'description' => 'Student has not partially or fully paid tuition.',
           ],
           [
               'key' => 'partially_paid',
               'label' => 'Partially Paid',
               'color' => 'primary',
               'description' => 'Student has made a partial tuition payment.',
           ],
           [
               'key' => 'fully_paid',
               'label' => 'Fully Paid',
               'color' => 'primary',
               'description' => 'Student has paid full tuition.',
           ],
           [
               'key' => 'enrolled',
               'label' => 'Enrolled',
               'color' => 'primary',
               'description' => 'Faculty/staff has officially enrolled the student in the system.',
           ],
           [
               'key' => 'withdrawn',
               'label' => 'Withdrawn',
               'color' => 'gray',
               'description' => 'Student has withdrawn from the institution.',
           ],
           [
               'key' => 'completed',
               'label' => 'Completed',
               'color' => 'success',
               'description' => 'Student has completed the program/course.',
           ],
        ]);

    }
}
