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
        if (!Schema::hasTable('project_team_members')) {
            Schema::create('project_team_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_recruitment_id')->constrained('project_recruitments')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->enum('role', ['creator', 'member'])->default('member');
                $table->enum('status', ['pending', 'active', 'rejected'])->default('pending');
                $table->timestamp('joined_at')->nullable();
                $table->timestamps();

                $table->unique(['project_recruitment_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_team_members');
    }
};
