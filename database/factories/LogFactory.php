<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class LogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'admin_id' => Admin::factory(),
            'action' => fake()->randomElement(['create', 'update', 'delete', 'login', 'logout']),
            'description' => fake()->sentence(),
            'loggable_type' => null,
            'loggable_id' => null,
            'old_values' => null,
            'new_values' => null,
        ];
    }
}
