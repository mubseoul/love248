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
        // Schema::table('subscription_plans', function (Blueprint $table) {
        //     $table->integer('subscription_level')->default(1)->after('status'); // 1=Free, 2=Premium, 3=Boosted
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('subscription_plans', function (Blueprint $table) {
        //     $table->dropColumn('subscription_level');
        // });
    }
};
