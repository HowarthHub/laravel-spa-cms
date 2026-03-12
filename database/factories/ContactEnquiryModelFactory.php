<?php

namespace Database\Factories;

use App\Models\ContactEnquiryModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactEnquiryModel>
 */
class ContactEnquiryModelFactory extends Factory
{
    protected $model = ContactEnquiryModel::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'subject' => fake()->sentence(),
            'message' => fake()->paragraphs(2, true),
            'status' => 'new',
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'read',
            'read_at' => now(),
        ]);
    }

    public function replied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'replied',
            'read_at' => now()->subHour(),
            'replied_at' => now(),
            'reply_note' => fake()->paragraph(),
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }
}
