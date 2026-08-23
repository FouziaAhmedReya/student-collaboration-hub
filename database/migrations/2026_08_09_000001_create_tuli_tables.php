<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ideas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('domain');
            $table->string('tech_stack');
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('required_skills');
            $table->integer('team_size')->default(4);
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('department');
            $table->text('skills');
            $table->text('interests')->nullable();
            $table->text('completed_projects')->nullable();
            $table->timestamps();
        });

        // Default project seed if needed
        DB::table('projects')->insert([
            'id' => 1,
            'title' => 'Student Productivity App',
            'required_skills' => 'Python, Flask, SQLite',
            'team_size' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('ideas');
    }
};
