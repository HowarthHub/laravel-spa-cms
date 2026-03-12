<?php

namespace Database\Factories;

use App\Models\MediaItemModel;
use App\Models\UserModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MediaItemModel>
 */
class MediaItemModelFactory extends Factory
{
    protected $model = MediaItemModel::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'alt_text' => fake()->sentence(4),
            'uploaded_by' => UserModel::factory(),
        ];
    }
}
