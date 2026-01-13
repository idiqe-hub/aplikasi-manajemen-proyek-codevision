<?php

namespace Database\Seeders;

use App\Models\Developer;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Project::factory()->count(8)->create();
        Developer::factory()->count(10)->create();

        // Tasks dibuat setelah project & developer ada
        Task::factory()->count(60)->create();
    }
}
