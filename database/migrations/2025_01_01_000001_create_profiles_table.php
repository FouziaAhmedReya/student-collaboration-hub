<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('profiles')) {
            Schema::create('profiles', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->unique();
                $table->string('profile_photo')->nullable();
                $table->string('department')->nullable();
                $table->string('semester')->nullable();
                $table->string('university')->nullable();
                $table->date('joined_date')->nullable();
                $table->text('about_me')->nullable();
                $table->string('preferred_location_name')->nullable();
                $table->string('preferred_location_address')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('profiles', function (Blueprint $table) {
                $legacyColumns = ['name', 'email', 'technical_skills', 'interests', 'completed_projects', 'portfolio_link', 'preferred_study_location'];
                foreach ($legacyColumns as $col) {
                    if (Schema::hasColumn('profiles', $col)) {
                        $table->dropColumn($col);
                    }
                }
                if (!Schema::hasColumn('profiles', 'user_id')) {
                    $table->foreignId('user_id')->after('id')->constrained('users')->cascadeOnDelete()->unique();
                }
                if (!Schema::hasColumn('profiles', 'profile_photo')) {
                    $table->string('profile_photo')->nullable()->after('user_id');
                }
                if (!Schema::hasColumn('profiles', 'department')) {
                    $table->string('department')->nullable();
                }
                if (!Schema::hasColumn('profiles', 'semester')) {
                    $table->string('semester')->nullable();
                }
                if (!Schema::hasColumn('profiles', 'university')) {
                    $table->string('university')->nullable();
                }
                if (!Schema::hasColumn('profiles', 'joined_date')) {
                    $table->date('joined_date')->nullable();
                }
                if (!Schema::hasColumn('profiles', 'about_me')) {
                    $table->text('about_me')->nullable();
                }
                if (!Schema::hasColumn('profiles', 'preferred_location_name')) {
                    $table->string('preferred_location_name')->nullable();
                }
                if (!Schema::hasColumn('profiles', 'preferred_location_address')) {
                    $table->string('preferred_location_address')->nullable();
                }
                if (!Schema::hasColumn('profiles', 'latitude')) {
                    $table->decimal('latitude', 10, 7)->nullable();
                }
                if (!Schema::hasColumn('profiles', 'longitude')) {
                    $table->decimal('longitude', 10, 7)->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};

