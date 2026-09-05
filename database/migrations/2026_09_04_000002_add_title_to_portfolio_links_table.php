<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('portfolio_links') && !Schema::hasColumn('portfolio_links', 'title')) {
            Schema::table('portfolio_links', function (Blueprint $table) {
                $table->string('title')->nullable()->after('profile_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('portfolio_links') && Schema::hasColumn('portfolio_links', 'title')) {
            Schema::table('portfolio_links', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }
    }
};
