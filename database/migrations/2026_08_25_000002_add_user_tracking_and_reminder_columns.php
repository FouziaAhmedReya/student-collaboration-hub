<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('created_by_id')->nullable()->after('project_id')->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->after('assigned_to')->constrained('users')->nullOnDelete();
            $table->timestamp('reminder_sent_at')->nullable()->after('notify_at');
        });

        Schema::table('project_meetings', function (Blueprint $table) {
            $table->foreignId('created_by_id')->nullable()->after('project_id')->constrained('users')->nullOnDelete();
            $table->timestamp('reminder_sent_at')->nullable()->after('google_calendar_event_id');
        });

        Schema::table('project_files', function (Blueprint $table) {
            $table->foreignId('created_by_id')->nullable()->after('project_id')->constrained('users')->nullOnDelete();
        });

        Schema::table('group_messages', function (Blueprint $table) {
            $table->foreignId('created_by_id')->nullable()->after('chat_group_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by_id');
            $table->dropConstrainedForeignId('assigned_user_id');
            $table->dropColumn('reminder_sent_at');
        });

        Schema::table('project_meetings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by_id');
            $table->dropColumn('reminder_sent_at');
        });

        Schema::table('project_files', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by_id');
        });

        Schema::table('group_messages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by_id');
        });
    }
};
