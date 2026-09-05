<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (!Schema::hasColumn('projects', 'title')) {
                    $table->string('title', 150)->nullable()->after('id');
                }
                if (!Schema::hasColumn('projects', 'required_skills')) {
                    $table->string('required_skills', 255)->nullable()->after('technologies');
                }
                if (!Schema::hasColumn('projects', 'team_size')) {
                    $table->unsignedSmallInteger('team_size')->default(4)->after('required_skills');
                }
            });

            // Populate title from name if name exists and title is null
            if (Schema::hasColumn('projects', 'name') && Schema::hasColumn('projects', 'title')) {
                DB::table('projects')->whereNull('title')->update([
                    'title' => DB::raw('`name`')
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('projects')) {
            Schema::table('projects', function (Blueprint $table) {
                if (Schema::hasColumn('projects', 'team_size')) {
                    $table->dropColumn('team_size');
                }
                if (Schema::hasColumn('projects', 'required_skills')) {
                    $table->dropColumn('required_skills');
                }
                if (Schema::hasColumn('projects', 'title')) {
                    $table->dropColumn('title');
                }
            });
        }
    }
};
