<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\TukangCukur;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('now', '+7 days');

        return [
            'booking_code' => 'BRB-' . date('Ymd') . '-' . str_pad(fake()->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT),
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->phoneNumber(),
            'tukang_cukur_id' => TukangCukur::factory(),
            'service_id' => Service::factory(),
            'booking_date' => $date->format('Y-m-d'),
            'booking_time' => fake()->randomElement(['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00']),
            'queue_number' => fake()->numberBetween(1, 50),
            'status' => 'pending',
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_date' => now()->toDateString(),
        ]);
    }
}
