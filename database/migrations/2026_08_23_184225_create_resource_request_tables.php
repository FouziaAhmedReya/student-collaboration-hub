<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_requests', function (Blueprint $table) {
            $table->id();

            $table->string('requester_name', 120);

            $table->string('course_code', 60);

            $table->string('course_name', 160)
                ->nullable();

            $table->string('title', 180);

            $table->text('description')
                ->nullable();

            $table->string('status', 30)
                ->default('open');

            $table->timestamps();

            $table->index('course_code');
            $table->index('status');
        });


        Schema::create('resource_uploads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('resource_request_id')
                ->constrained('resource_requests')
                ->cascadeOnDelete();

            $table->string('uploader_name', 120);

            $table->string('title', 180);

            $table->string('file_name');

            $table->text('file_url');

            $table->string('cloudinary_public_id');

            $table->string('resource_type', 30);

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('resource_uploads');

        Schema::dropIfExists('resource_requests');
    }
};
