<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('video_id');
            $table->unsignedInteger('streamer_id');
            $table->unsignedInteger('user_id');
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_sales');
    }
}; 