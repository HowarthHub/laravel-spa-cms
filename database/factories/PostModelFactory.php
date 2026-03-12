<?php

namespace Database\Factories;

use App\Models\PostModel;
use App\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PostModel>
 */
class PostModelFactory extends Factory
{
    protected $model = PostModel::class;

    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => [['type' => 'paragraph', 'content' => fake()->paragraphs(3, true)]],
            'excerpt' => fake()->sentence(),
            'status' => 'draft',
            'author_id' => UserModel::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'scheduled',
            'published_at' => now()->addWeek(),
        ]);
    }
}
