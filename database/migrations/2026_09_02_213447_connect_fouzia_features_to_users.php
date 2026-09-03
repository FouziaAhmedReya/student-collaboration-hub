<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->unique()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();
        });

        Schema::table('resource_requests', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained('users')
                ->nullOnDelete();
        });

        Schema::table('resource_uploads', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('resource_request_id')
                ->constrained('users')
                ->nullOnDelete();
        });

        Schema::create('tutor_ratings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tutor_id')
                ->constrained('tutors')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->decimal('rating', 2, 1);

            $table->text('review')->nullable();

            $table->timestamps();

            // One student can rate a tutor only once.
            $table->unique(['tutor_id', 'user_id']);
        });

        // Connect old tutor profiles with registered tutor accounts
        // when both records use the same email.
        DB::table('tutors')
            ->whereNull('user_id')
            ->whereNotNull('email')
            ->orderBy('id')
            ->each(function ($tutor): void {
                $user = DB::table('users')
                    ->whereRaw(
                        'LOWER(email) = ?',
                        [strtolower($tutor->email)]
                    )
                    ->where('role', 'tutor')
                    ->where('status', 'approved')
                    ->first();

                if (! $user) {
                    return;
                }

                $alreadyConnected = DB::table('tutors')
                    ->where('user_id', $user->id)
                    ->exists();

                if (! $alreadyConnected) {
                    DB::table('tutors')
                        ->where('id', $tutor->id)
                        ->update([
                            'user_id' => $user->id,
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutor_ratings');

        Schema::table('resource_uploads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('resource_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('tutors', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};