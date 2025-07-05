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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('title');
            $table->string('thumbnail')->nullable();
            $table->string('video')->nullable();
            $table->unsignedInteger('price')->default(0);
            $table->enum('free_for_subs', ['yes', 'no']);
            $table->integer('views')->default(0);
            $table->string('disk');
            $table->unsignedInteger('category_id');
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
}; 