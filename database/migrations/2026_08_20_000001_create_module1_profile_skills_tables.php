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
        // 1. Profiles table
        if (!Schema::hasTable('profiles')) {
            Schema::create('profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
                $table->string('profile_photo')->nullable();
                $table->string('department')->nullable();
                $table->string('semester')->nullable();
                $table->string('university')->nullable();
                $table->string('phone')->nullable();
                $table->string('joined_date')->nullable();
                $table->text('about_me')->nullable();
                $table->text('bio')->nullable();
                $table->string('preferred_location_name')->nullable();
                $table->string('preferred_location_address')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->timestamps();
            });
        }

        // 2. Skills table (supports both 0-100 proficiency and level)
        if (!Schema::hasTable('skills')) {
            Schema::create('skills', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->string('name');
                $table->unsignedTinyInteger('proficiency')->nullable()->default(50); // 0-100
                $table->string('proficiency_level')->default('Intermediate'); // Beginner, Intermediate, Advanced, Expert
                $table->string('category')->nullable(); // e.g. Frontend, Backend, Database, Cloud, Mobile
                $table->timestamps();
            });
        }

        // 3. Interests table
        if (!Schema::hasTable('interests')) {
            Schema::create('interests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->string('name');
                $table->string('category')->nullable();
                $table->timestamps();
            });
        }

        // 4. Department Interests for suggestions
        if (!Schema::hasTable('department_interests')) {
            Schema::create('department_interests', function (Blueprint $table) {
                $table->id();
                $table->string('department');
                $table->string('name');
                $table->timestamps();
            });
        }

        // 5. Student Projects table (SEPARATE from existing team/recruitment projects table)
        if (!Schema::hasTable('student_projects')) {
            Schema::create('student_projects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('technologies')->nullable();
                $table->string('project_url')->nullable();
                $table->string('repo_url')->nullable();
                $table->string('completed_date')->nullable();
                $table->timestamps();
            });

            // If legacy projects table exists with profile_id column, preserve and copy existing records to student_projects
            if (Schema::hasTable('projects') && Schema::hasColumn('projects', 'profile_id')) {
                $legacyProjects = DB::table('projects')->get();
                foreach ($legacyProjects as $lp) {
                    if (isset($lp->profile_id)) {
                        DB::table('student_projects')->insert([
                            'id' => $lp->id,
                            'profile_id' => $lp->profile_id,
                            'title' => $lp->name ?? ($lp->title ?? 'Untitled Project'),
                            'description' => $lp->description ?? null,
                            'technologies' => $lp->technologies ?? null,
                            'created_at' => $lp->created_at ?? now(),
                            'updated_at' => $lp->updated_at ?? now(),
                        ]);
                    }
                }
            }
        }

        // 6. Portfolio Links table (supports both title and platform)
        if (!Schema::hasTable('portfolio_links')) {
            Schema::create('portfolio_links', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
                $table->string('title')->nullable();
                $table->string('platform')->nullable(); // GitHub, LinkedIn, Portfolio Website, LeetCode, etc.
                $table->string('url');
                $table->timestamps();
            });
        }

        // Seed initial department interest suggestions if table was just created / empty
        if (Schema::hasTable('department_interests') && DB::table('department_interests')->count() === 0) {
            DB::table('department_interests')->insert([
                ['department' => 'Computer Science & Engineering', 'name' => 'Artificial Intelligence & Machine Learning', 'created_at' => now(), 'updated_at' => now()],
                ['department' => 'Computer Science & Engineering', 'name' => 'Cybersecurity & Ethical Hacking', 'created_at' => now(), 'updated_at' => now()],
                ['department' => 'Computer Science & Engineering', 'name' => 'Full-Stack Web Development', 'created_at' => now(), 'updated_at' => now()],
                ['department' => 'Computer Science & Engineering', 'name' => 'Cloud Computing & Distributed Systems', 'created_at' => now(), 'updated_at' => now()],
                ['department' => 'Software Engineering', 'name' => 'DevOps & CI/CD Pipelines', 'created_at' => now(), 'updated_at' => now()],
                ['department' => 'Software Engineering', 'name' => 'Software Architecture & System Design', 'created_at' => now(), 'updated_at' => now()],
                ['department' => 'Software Engineering', 'name' => 'Mobile App Engineering (iOS/Android/Flutter)', 'created_at' => now(), 'updated_at' => now()],
                ['department' => 'Information Technology', 'name' => 'Network Administration & Cloud Security', 'created_at' => now(), 'updated_at' => now()],
                ['department' => 'Information Technology', 'name' => 'Database Administration & Big Data', 'created_at' => now(), 'updated_at' => now()],
                ['department' => 'Data Science & Analytics', 'name' => 'Data Engineering & Predictive Modeling', 'created_at' => now(), 'updated_at' => now()],
                ['department' => 'Data Science & Analytics', 'name' => 'Natural Language Processing (NLP) & LLMs', 'created_at' => now(), 'updated_at' => now()],
                ['department' => 'Electrical & Electronic Engineering', 'name' => 'Embedded Systems & IoT Robotics', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portfolio_links');
        Schema::dropIfExists('student_projects');
        Schema::dropIfExists('department_interests');
        Schema::dropIfExists('interests');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('profiles');
    }
};
