<?php

namespace Database\Factories;

use App\Models\MenuModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<MenuModel>
 */
class MenuModelFactory extends Factory
{
    protected $model = MenuModel::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => $name,
            'handle' => Str::slug($name),
        ];
    }
}
