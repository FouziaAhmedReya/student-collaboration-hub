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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->default('Workshop'); // Workshop, Seminar, Hackathon, Webinar
            $table->text('description');
            $table->string('target_skills')->nullable();
            $table->dateTime('event_date')->nullable();
            $table->string('location')->nullable();
            $table->string('organizer')->nullable();
            $table->timestamps();
        });

        // Seed initial upcoming events for BRAC University students
        DB::table('events')->insert([
            [
                'title' => 'Hands-On Generative AI & Gemini API Workshop',
                'type' => 'Workshop',
                'description' => 'Learn how to build AI-powered web applications using Laravel, Python, and Google Gemini 2.5 Flash API.',
                'target_skills' => 'Python, Gemini API, Laravel, Machine Learning',
                'event_date' => now()->addDays(5)->setTime(14, 0),
                'location' => 'UB20101 Computer Lab / Zoom',
                'organizer' => 'CSE Department',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'BRACU National Campus Hackathon 2026',
                'type' => 'Hackathon',
                'description' => '36-hour sprint competition to build innovative solutions for campus productivity, education, and student collaboration.',
                'target_skills' => 'Fullstack Web, Mobile App, React, Node.js, UI/UX',
                'event_date' => now()->addDays(12)->setTime(9, 0),
                'location' => 'Auditorium & Multipurpose Hall',
                'organizer' => 'BRACU Computer Club',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Modern Cloud Computing & Microservices Seminar',
                'type' => 'Seminar',
                'description' => 'Industry experts share insights on Docker, Kubernetes, AWS Cloud Architecture, and DevOps best practices.',
                'target_skills' => 'Docker, AWS, Microservices, DevOps, Cloud',
                'event_date' => now()->addDays(8)->setTime(16, 30),
                'location' => 'UB30204 Conference Room',
                'organizer' => 'School of Data & Computer Science',
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
        Schema::dropIfExists('events');
    }
};
