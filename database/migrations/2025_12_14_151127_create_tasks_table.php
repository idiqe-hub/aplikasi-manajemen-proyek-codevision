<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
        $table->id();

        $table->foreignId('project_id')
              ->constrained('projects')
              ->cascadeOnDelete();

        $table->foreignId('developer_id')
              ->nullable()
              ->constrained('developers')
              ->nullOnDelete();

        $table->string('title');
        $table->text('description')->nullable();

        $table->enum('status', ['todo','in_progress','done'])->default('todo');
        $table->unsignedTinyInteger('progress')->default(0);

        $table->date('deadline')->nullable();
        $table->unsignedSmallInteger('estimated_hours')->nullable();
        $table->unsignedSmallInteger('actual_hours')->nullable();

        $table->timestamps();

        $table->index(['project_id','developer_id','status']);
        $table->index(['deadline']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
