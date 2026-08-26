<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('role')
                ->default('student')
                ->after('email');

            $table->string('student_id')
                ->nullable()
                ->after('role');

            $table->string('department')
                ->nullable()
                ->after('student_id');

            $table->string('phone')
                ->nullable()
                ->after('department');

        });
    }


    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'role',
                'student_id',
                'department',
                'phone'
            ]);

        });
    }
};