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
        if (!Schema::hasTable('project_recruitments')) {
            Schema::create('project_recruitments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
                $table->string('title');
                $table->text('description');
                $table->string('course');
                $table->string('project_type')->default('Course Project'); // Course Project, Capstone/Thesis, Hackathon, Research, Open Source
                $table->unsignedInteger('required_members')->default(4);
                $table->unsignedInteger('current_members')->default(1);
                $table->string('required_skills')->nullable(); // Comma-separated tags
                $table->enum('recruitment_status', ['open', 'closed'])->default('open');
                $table->date('meeting_date')->nullable();
                $table->string('meeting_time')->nullable();
                $table->string('location_name')->nullable();
                $table->string('location_address')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_recruitments');
    }
};
