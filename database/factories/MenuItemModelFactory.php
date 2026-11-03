<?php

namespace Database\Factories;

use App\Models\MenuItemModel;
use App\Models\MenuModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuItemModel>
 */
class MenuItemModelFactory extends Factory
{
    protected $model = MenuItemModel::class;

    public function definition(): array
    {
        return [
            'menu_id' => MenuModel::factory(),
            'label' => fake()->words(2, true),
            'type' => 'custom',
            'url' => fake()->url(),
            'target' => '_self',
            'sort_order' => 0,
        ];
    }
}
