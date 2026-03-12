<?php

namespace Database\Factories;

use App\Models\ServiceModel;
use App\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ServiceModel>
 */
class ServiceModelFactory extends Factory
{
    protected $model = ServiceModel::class;

    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'short_description' => fake()->sentence(),
            'content' => [['type' => 'paragraph', 'content' => fake()->paragraphs(3, true)]],
            'status' => 'draft',
            'sort_order' => 0,
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
}
