<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Used until authentication is fully connected.
            $table->string('owner_token', 64)->nullable()->index();

            $table->string('title', 150);
            $table->string('author', 120);
            $table->decimal('price', 10, 2);
            $table->string('course', 120);
            $table->string('category', 60);
            $table->string('condition', 30);
            $table->text('description')->nullable();

            // active, reserved, sold
            $table->string('status', 20)->default('active');

            $table->string('seller_name', 120);
            $table->string('seller_email', 150);
            $table->string('seller_phone', 30)->nullable();

            // Cloudinary image information
            $table->string('original_image_name');
            $table->string('image_public_id')->unique();
            $table->text('image_url');
            $table->string('image_resource_type', 30)->default('image');
            $table->string('image_format', 20)->nullable();
            $table->string('image_mime_type', 100)->nullable();
            $table->unsignedBigInteger('image_bytes')->default(0);

            $table->timestamps();

            $table->index('title');
            $table->index('course');
            $table->index('category');
            $table->index('condition');
            $table->index('status');
        });

        Schema::create('book_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('buyer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('buyer_token', 64)->nullable()->index();

            $table->string('buyer_name', 120);
            $table->string('buyer_email', 150);
            $table->string('buyer_phone', 30)->nullable();
            $table->text('message')->nullable();

            // pending, accepted, rejected, cancelled
            $table->string('status', 20)->default('pending');
            $table->timestamp('responded_at')->nullable();

            $table->timestamps();

            $table->index(['book_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_orders');
        Schema::dropIfExists('books');
    }
};