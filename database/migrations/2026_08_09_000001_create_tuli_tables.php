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

        // Seed default initial project
        DB::table('projects')->insert([
            'id' => 1,
            'title' => 'Student Productivity App',
            'required_skills' => 'Python, Flask, SQLite',
            'team_size' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed default mock students matching tuli_saha
        DB::table('students')->insert([
            [
                'name' => 'Alice Smith',
                'department' => 'Computer Science',
                'skills' => 'Python, Flask, SQLite, React',
                'interests' => 'AI, Productivity Tools',
                'completed_projects' => 'Portfolio Website, CLI Task Manager',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bob Johnson',
                'department' => 'Software Engineering',
                'skills' => 'React, Figma, UI Design, JavaScript',
                'interests' => 'UX/UI, Front-end',
                'completed_projects' => 'E-commerce UI, Mobile Redesign',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Charlie Brown',
                'department' => 'Computer Science',
                'skills' => 'Figma, UI Design, CSS, HTML',
                'interests' => 'Design Systems, Web Design',
                'completed_projects' => 'Blog Template, Personal Portfolio',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Diana Prince',
                'department' => 'Information Technology',
                'skills' => 'Node.js, MongoDB, Express, React',
                'interests' => 'Fullstack Development, Cloud',
                'completed_projects' => 'Task Manager API, Chat App',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Evan Wright',
                'department' => 'Computer Science',
                'skills' => 'Python, Machine Learning, TensorFlow, SQL',
                'interests' => 'Data Science, Intelligent Systems',
                'completed_projects' => 'Image Classifier, Sales Predictor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Fiona Gallagher',
                'department' => 'Software Engineering',
                'skills' => 'Java, Spring Boot, MySQL',
                'interests' => 'Backend Systems, Microservices',
                'completed_projects' => 'Library Management System, REST API',
                'created_at' => now(),
                'updated_at' => now(),
            ],
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
