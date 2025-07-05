<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mercado_accounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user')->nullable();
            $table->string('access_token', 500)->nullable();
            $table->string('expires_in')->nullable();
            $table->string('scope', 500)->nullable();
            $table->integer('user_id')->nullable();
            $table->string('refresh_token', 500)->nullable();
            $table->string('public_key', 500)->nullable();
            $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mercado_accounts');
    }
}; 