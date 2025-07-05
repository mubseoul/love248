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
        Schema::create('streaming_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('streamer_id');
            $table->unsignedBigInteger('streamer_time_id');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('streamer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('streamer_time_id')->references('id')->on('streaming_times')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('streaming_prices');
    }
};
