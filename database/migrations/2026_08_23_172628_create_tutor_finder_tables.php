<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutors', function (Blueprint $table) {
            $table->id();

            $table->string('name', 120);
            $table->string('email', 160)->nullable();

            $table->string('subject', 160);
            $table->string('availability', 160);

            $table->decimal('rating', 2, 1)->default(0);

            $table->text('bio')->nullable();

            // Tutor profile photo from Cloudinary
            $table->text('profile_image_url')->nullable();

            $table->string(
                'profile_image_public_id'
            )->nullable();

            $table->string(
                'profile_image_resource_type',
                30
            )->nullable();

            $table->timestamps();

            $table->index('subject');
            $table->index('rating');
        });


        Schema::create(
            'tutor_materials',
            function (Blueprint $table) {

                $table->id();

                $table->foreignId('tutor_id')
                    ->constrained('tutors')
                    ->cascadeOnDelete();

                $table->string('title', 160);

                $table->string('file_name');

                // Cloudinary file data
                $table->text('file_url');

                $table->string(
                    'cloudinary_public_id'
                );

                $table->string(
                    'resource_type',
                    30
                );

                $table->timestamps();
            }
        );
    }


    public function down(): void
    {
        Schema::dropIfExists('tutor_materials');

        Schema::dropIfExists('tutors');
    }
};