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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('subscription_name')->nullable();
            $table->string('subscription_price')->nullable();
            $table->integer('days')->default(0);
            $table->integer('Is_purchase')->default(0);
            $table->text('details')->nullable();
            $table->integer('status')->default(0);
            $table->integer('subscription_level')->default(1); // 1=Free, 2=Premium, 3=Boosted
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
}; 