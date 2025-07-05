<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('streamer_availability', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('streamer_id');
            $table->string('start_time');
            $table->string('end_time')->nullable();
            $table->text('days_of_week')->nullable();
            $table->decimal('tokens_per_minute', 8, 2);
            $table->timestamps();

            $table->foreign('streamer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('streamer_availability');
    }
}; 