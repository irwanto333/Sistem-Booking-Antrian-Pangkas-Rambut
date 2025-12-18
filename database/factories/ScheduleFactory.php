<?php

namespace Database\Factories;

use App\Models\TukangCukur;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tukang_cukur_id' => TukangCukur::factory(),
            'day_of_week' => fake()->numberBetween(0, 6),
            'open_time' => '08:00',
            'close_time' => '20:00',
            'is_available' => true,
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
        ]);
    }
}
