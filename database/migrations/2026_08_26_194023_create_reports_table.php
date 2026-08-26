<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{

    public function up(): void
    {

        Schema::create('reports', function (Blueprint $table) {


            $table->id();


            // User who submitted the report
            $table->foreignId('reporter_id')
                ->constrained('users')
                ->cascadeOnDelete();



            // User being reported
            $table->foreignId('reported_user_id')
                ->constrained('users')
                ->cascadeOnDelete();



            $table->string('reason');


            $table->text('description')
                ->nullable();



            $table->enum('status', [

                'pending',

                'resolved',

                'rejected'

            ])
            ->default('pending');



            $table->timestamps();


        });

    }





    public function down(): void
    {

        Schema::dropIfExists('reports');

    }

};