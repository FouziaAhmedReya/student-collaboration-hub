<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title', 150);
            $table->text('agenda')->nullable();
            $table->string('organizer', 120); // team leader's name
            $table->dateTime('meeting_time');
            $table->date('deadline')->nullable(); // e.g. "decision due by"
            $table->string('google_calendar_event_id')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'meeting_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_meetings');
    }
};