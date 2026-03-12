<?php

namespace Database\Factories;

use App\Models\PageModel;
use App\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PageModel>
 */
class PageModelFactory extends Factory
{
    protected $model = PageModel::class;

    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => [['type' => 'paragraph', 'content' => fake()->paragraphs(3, true)]],
            'excerpt' => fake()->sentence(),
            'template' => 'default',
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

    public function withParent(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => PageModel::factory(),
        ]);
    }
}
