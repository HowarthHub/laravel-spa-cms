<?php

namespace Database\Factories;

use App\Models\SettingModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SettingModel>
 */
class SettingModelFactory extends Factory
{
    protected $model = SettingModel::class;

    public function definition(): array
    {
        return [
            'group' => 'general',
            'key' => fake()->unique()->slug(2),
            'value' => fake()->sentence(),
            'type' => 'text',
        ];
    }
}
