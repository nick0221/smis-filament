<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FacultyStaff>
 */
class FacultyStaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
             'first_name' => fake()->firstName(),
             'middle_name' => fake()->firstName(),
             'last_name' => fake()->lastName(),
             'extension_name' => fake()->lastName(),
             'dob' => fake()->date(),
             'gender' => fake()->randomElement(['male', 'female']),
             'phone' => fake()->phoneNumber(),
             'email' => fake()->unique()->safeEmail(),
             'address' => fake()->address(),
             'user_id' => fake()->numberBetween(1, 10),
             'designation_id' => fake()->numberBetween(1, 10),
             'department' => fake()->company(),
             'photo_path' => fake()->imageUrl(),

        ];
    }
}
