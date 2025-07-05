<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('followables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('user_id');
            $table->string('followable_type');
            $table->unsignedBigInteger('followable_id');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['followable_type', 'followable_id']);
            $table->index(['followable_type', 'accepted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('followables');
    }
}; 