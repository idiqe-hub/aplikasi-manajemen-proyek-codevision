<?php

namespace Database\Factories;

use App\Models\Developer;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        $status = fake()->randomElement(['todo', 'in_progress', 'done']);
        $progress = match ($status) {
            'todo' => fake()->numberBetween(0, 20),
            'in_progress' => fake()->numberBetween(30, 90),
            'done' => 100,
        };

        $deadline = fake()->dateTimeBetween('-10 days', '+20 days');

        return [
            'project_id' => Project::inRandomOrder()->value('id'),
            'developer_id' => fake()->boolean(85) ? Developer::inRandomOrder()->value('id') : null,
            'title' => fake()->sentence(4),
            'description' => fake()->sentence(12),
            'status' => $status,
            'progress' => $progress,
            'deadline' => $deadline->format('Y-m-d'),
            'estimated_hours' => fake()->numberBetween(2, 24),
            'actual_hours' => $status === 'done' ? fake()->numberBetween(2, 30) : null,
        ];
    }
}
