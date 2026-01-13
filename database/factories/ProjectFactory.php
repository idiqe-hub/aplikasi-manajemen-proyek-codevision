<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-30 days', 'now');
        $end   = (clone $start);
        $end->modify('+'.fake()->numberBetween(7, 45).' days');

        return [
            'name' => 'Project ' . fake()->unique()->words(2, true),
            'client_name' => fake()->company(),
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'status' => fake()->randomElement(['planned', 'on_progress', 'completed']),
            'description' => fake()->sentence(12),
        ];
    }
}
