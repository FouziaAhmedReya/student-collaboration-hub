<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('group_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_group_id')->constrained()->cascadeOnDelete();
            $table->string('sender_name', 120);
            $table->text('message');
            $table->timestamps();

            $table->index(['chat_group_id', 'created_at']);
        });

        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_group_id')->constrained()->cascadeOnDelete();
            $table->string('title', 150);
            $table->dateTime('meeting_time');
            $table->string('google_calendar_event_id')->nullable();
            $table->timestamps();

            $table->index(['chat_group_id', 'meeting_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meetings');
        Schema::dropIfExists('group_messages');
        Schema::dropIfExists('chat_groups');
    }
};
