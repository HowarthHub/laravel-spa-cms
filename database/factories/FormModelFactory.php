<?php

namespace Database\Factories;

use App\Models\FormModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<FormModel>
 */
class FormModelFactory extends Factory
{
    protected $model = FormModel::class;

    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'name' => $name,
            'handle' => Str::slug($name),
            'fields' => [
                ['label' => 'Name', 'type' => 'text', 'required' => true, 'placeholder' => ''],
                ['label' => 'Email', 'type' => 'email', 'required' => true, 'placeholder' => ''],
                ['label' => 'Message', 'type' => 'textarea', 'required' => false, 'placeholder' => ''],
            ],
            'success_message' => 'Thank you for your submission.',
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
