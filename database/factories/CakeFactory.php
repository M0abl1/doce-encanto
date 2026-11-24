<?php

namespace Database\Factories;

use App\Models\Cake;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Cake>
 */
class CakeFactory extends Factory
{
    public function definition(): array
    {
        $name = 'Bolo '.Str::title(fake()->unique()->words(2, true));

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'summary' => fake()->sentence(8),
            'description' => fake()->paragraph(),
            'price_cents' => fake()->numberBetween(40, 250) * 100 + 90,
            'serves' => fake()->numberBetween(8, 30).' fatias',
            'color' => fake()->randomElement(['#C2416B', '#8B5A3C', '#D98E4A', '#E9B8C6', '#6B8E4E']),
            'is_featured' => false,
            'is_active' => true,
        ];
    }

    public function featured(): static
    {
        return $this->state(['is_featured' => true]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
