<?php

namespace Database\Factories;

use App\Models\CategoryModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CategoryModel>
 */
class CategoryModelFactory extends Factory
{
    protected $model = CategoryModel::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'sort_order' => 0,
        ];
    }

    public function withParent(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => CategoryModel::factory(),
        ]);
    }
}
