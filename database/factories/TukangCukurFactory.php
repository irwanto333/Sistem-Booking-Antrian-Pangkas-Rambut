<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TukangCukurFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name('male'),
            'phone' => fake()->phoneNumber(),
            'photo' => null,
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
