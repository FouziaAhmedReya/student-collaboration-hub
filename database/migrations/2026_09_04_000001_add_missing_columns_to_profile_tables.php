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
        Schema::table('profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('profiles', 'phone')) {
                $table->string('phone')->nullable()->after('university');
            }
            if (!Schema::hasColumn('profiles', 'bio')) {
                $table->text('bio')->nullable()->after('about_me');
            }
        });

        Schema::table('skills', function (Blueprint $table) {
            if (!Schema::hasColumn('skills', 'proficiency_level')) {
                $table->string('proficiency_level')->default('Intermediate')->after('proficiency');
            }
            if (!Schema::hasColumn('skills', 'category')) {
                $table->string('category')->nullable()->after('proficiency_level');
            }
        });

        Schema::table('interests', function (Blueprint $table) {
            if (!Schema::hasColumn('interests', 'category')) {
                $table->string('category')->nullable()->after('name');
            }
        });

        Schema::table('portfolio_links', function (Blueprint $table) {
            if (!Schema::hasColumn('portfolio_links', 'title')) {
                $table->string('title')->nullable()->after('profile_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            if (Schema::hasColumn('profiles', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('profiles', 'bio')) {
                $table->dropColumn('bio');
            }
        });

        Schema::table('skills', function (Blueprint $table) {
            if (Schema::hasColumn('skills', 'category')) {
                $table->dropColumn('category');
            }
            if (Schema::hasColumn('skills', 'proficiency_level')) {
                $table->dropColumn('proficiency_level');
            }
        });

        Schema::table('interests', function (Blueprint $table) {
            if (Schema::hasColumn('interests', 'category')) {
                $table->dropColumn('category');
            }
        });

        Schema::table('portfolio_links', function (Blueprint $table) {
            if (Schema::hasColumn('portfolio_links', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
};
