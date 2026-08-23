<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tutors', function (Blueprint $table) {

            $table
                ->string('owner_token_hash', 64)
                ->nullable();
        });
    }


    public function down(): void
    {
        Schema::table('tutors', function (Blueprint $table) {

            $table->dropColumn('owner_token_hash');
        });
    }
};