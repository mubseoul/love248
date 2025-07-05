<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->timestamp('last_refreshed_at')->nullable()->after('status');
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->timestamp('last_refreshed_at')->nullable()->after('status');
        });

        // Set initial values for existing records
        DB::statement('UPDATE galleries SET last_refreshed_at = updated_at WHERE last_refreshed_at IS NULL');
        DB::statement('UPDATE videos SET last_refreshed_at = updated_at WHERE last_refreshed_at IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropColumn('last_refreshed_at');
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn('last_refreshed_at');
        });
    }
}; 