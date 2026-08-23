<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meeting_id')->nullable()->constrained('project_meetings')->nullOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->string('uploaded_by', 120);
            $table->string('original_name');
            $table->string('cloudinary_public_id');
            $table->string('secure_url');
            $table->string('resource_type', 30);
            $table->unsignedBigInteger('bytes')->default(0);
            $table->timestamps();

            $table->index(['project_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_files');
    }
};