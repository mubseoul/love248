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
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->string('title')->nullable();
            $table->string('thumbnail')->nullable();
            $table->decimal('price', 8, 2)->default(0.00);
            $table->enum('free_for_subs', ['no', 'yes'])->default('no');
            $table->integer('views')->default(0);
            $table->string('disk')->nullable();
            $table->integer('category_id')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
}; 