<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_sales', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->integer('gallery_id')->nullable();
            $table->integer('streamer_id')->nullable();
            $table->decimal('price', 8, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_sales');
    }
}; 