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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tier_id');
            $table->integer('streamer_id');
            $table->unsignedInteger('subscriber_id');
            $table->timestamp('subscription_date')->useCurrent();
            $table->timestamp('subscription_expires')->nullable();
            $table->enum('status', ['Active', 'Canceled'])->default('Active');
            $table->integer('subscription_tokens');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
}; 