<?php

namespace Database\Factories;

use App\Models\FormModel;
use App\Models\FormSubmissionModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FormSubmissionModel>
 */
class FormSubmissionModelFactory extends Factory
{
    protected $model = FormSubmissionModel::class;

    public function definition(): array
    {
        return [
            'form_id' => FormModel::factory(),
            'data' => [
                'Name' => fake()->name(),
                'Email' => fake()->safeEmail(),
                'Message' => fake()->paragraph(),
            ],
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }
}
