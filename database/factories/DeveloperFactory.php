<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DeveloperFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'role' => fake()->randomElement(['frontend', 'backend', 'fullstack', 'pm']),
            'skill' => fake()->randomElement(['Laravel', 'React', 'UI/UX', 'MySQL', 'API', 'Flutter']),
        ];
    }
}
