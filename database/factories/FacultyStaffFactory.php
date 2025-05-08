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
        $firstname = fake()->firstName();
        $lastname = fake()->lastName();
        $initials = strtoupper(substr($firstname, 0, 1) . substr($lastname, 0, 1));
        $textColor = substr(md5(rand()), 0, 6); // e.g., 'a1b2c3'


        return [
             'first_name' => $firstname,
             'middle_name' => fake()->lastName(),
             'last_name' => $lastname,
             'extension_name' => fake()->randomElement(['Jr.', 'Sr.', 'I', 'II', 'III', '']),
             'dob' => fake()->date(),
             'gender' => fake()->randomElement(['male', 'female']),
             'phone' => fake()->phoneNumber(),
             'email' => fake()->unique()->safeEmail(),
             'address' => fake()->address(),
             'user_id' => fake()->numberBetween(1, 10),
             'designation_id' => fake()->numberBetween(1, 7),
             'department' => fake()->company(),
             'photo_path' => fn () => "https://placehold.co/600x400/eeeeee/grey?font=roboto&text=" . $initials,

        ];
    }
}
