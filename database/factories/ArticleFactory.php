<?php

namespace Database\Factories;

use App\Enums\ArticleStatus;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'topic_id' => Topic::factory(),
            'author_id' => User::factory(),
            'body' => fake()->paragraph(10, true),
            'status' => ArticleStatus::Published,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => ArticleStatus::Draft,
        ]);
    }
}
