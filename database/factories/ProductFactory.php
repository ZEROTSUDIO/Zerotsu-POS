<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->sentence();
        return [
            'code' => fake()->unique()->numerify('P-####'),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->text(300),
            'specification' => [
                'color' => fake()->colorName(),
                'weight' => fake()->numberBetween(1, 100) . 'kg',
                'dimensions' => fake()->numberBetween(10, 100) . 'x' . fake()->numberBetween(10, 100) . 'cm',
                'material' => fake()->randomElement(['Stone', 'Concrete', 'Marble']),
            ],
            'category_id' => Category::factory(),
        ];
    }
}
