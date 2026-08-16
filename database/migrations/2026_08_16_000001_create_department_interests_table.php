<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('department_interests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('department');   // e.g. 'CSE', 'EEE', 'General'
            $table->string('name');         // e.g. 'Artificial Intelligence'
            $table->timestamps();

            $table->index('department');
            $table->unique(['department', 'name']); // no duplicates per department
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('department_interests');
    }
};
