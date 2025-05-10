<?php

namespace Database\Factories;

use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Section>
 */
class SectionFactory extends Factory
{
    protected $model = Section::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gemstoneNames = [
           'Amber', 'Amethyst', 'Aquamarine', 'Diamond', 'Emerald',
           'Garnet', 'Jade', 'Opal', 'Pearl', 'Ruby',
           'Sapphire', 'Topaz', 'Turquoise', 'Zircon'
        ];

        return [
            'section_name' => $this->faker->unique()->randomElement($gemstoneNames),
            'grade_level_id' => fake()->numberBetween(1, 10),
        ];
    }
}
