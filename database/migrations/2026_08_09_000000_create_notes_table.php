<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('department', 100);
            $table->string('course', 120);
            $table->string('semester', 50);
            $table->string('original_name');
            $table->string('public_id')->unique();
            $table->text('secure_url');
            $table->string('resource_type', 30)->default('raw');
            $table->string('format', 20)->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('bytes')->default(0);
            $table->unsignedInteger('downloads_count')->default(0);
            $table->timestamps();

            $table->index(['department', 'course', 'semester']);
            $table->index('title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
