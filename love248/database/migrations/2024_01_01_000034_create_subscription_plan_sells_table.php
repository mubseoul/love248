<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plan_sells', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('subscription_plan', 100);
            $table->decimal('price', 10, 2);
            $table->dateTime('expire_date');
            $table->string('status');
            $table->json('upgrade_data')->nullable();
            $table->string('gateway');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plan_sells');
    }
}; 