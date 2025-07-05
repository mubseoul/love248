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
        // Check if price_new column exists (from previous migration)
        if (Schema::hasColumn('videos', 'price_new')) {
            // Rename price_new back to price using raw SQL
            DB::statement('ALTER TABLE videos CHANGE price_new price DECIMAL(8,2) DEFAULT 0.00');
        } elseif (Schema::hasColumn('videos', 'price')) {
            // Check if the price column is already decimal
            $columnInfo = DB::select("SHOW COLUMNS FROM videos LIKE 'price'");
            if (!empty($columnInfo) && strpos($columnInfo[0]->Type, 'decimal') === false) {
                // Column exists but is not decimal, convert it
                DB::statement('ALTER TABLE videos MODIFY COLUMN price DECIMAL(8,2) DEFAULT 0.00');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if price column exists and is decimal
        if (Schema::hasColumn('videos', 'price')) {
            $columnInfo = DB::select("SHOW COLUMNS FROM videos LIKE 'price'");
            if (!empty($columnInfo) && strpos($columnInfo[0]->Type, 'decimal') !== false) {
                // Change back to integer
                DB::statement('ALTER TABLE videos MODIFY COLUMN price INT UNSIGNED DEFAULT 0');
            }
        }
    }
}; 