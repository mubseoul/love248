<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('streaming_prices') && Schema::hasColumn('streaming_prices', 'tokens_per_minute')) {
            Schema::table('streaming_prices', function (Blueprint $table) {
                $table->dropColumn('tokens_per_minute');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('streaming_prices')) {
            Schema::table('streaming_prices', function (Blueprint $table) {
                $table->decimal('tokens_per_minute', 8, 2)->after('streamer_time_id');
            });
        }
    }
};
