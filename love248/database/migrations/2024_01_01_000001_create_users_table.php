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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('skin_tone')->nullable();
            $table->string('dob', 10)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('profile_picture')->nullable();
            $table->string('cover_picture')->nullable();
            $table->string('headline')->nullable();
            $table->text('about')->nullable();
            $table->decimal('tokens', 8, 2)->default(0.00);
            $table->enum('is_streamer', ['yes', 'no'])->default('no');
            $table->enum('is_streamer_verified', ['yes', 'no'])->default('no');
            $table->enum('streamer_verification_sent', ['yes', 'no'])->default('no');
            $table->enum('live_status', ['online', 'offline'])->default('offline');
            $table->integer('popularity')->default(0);
            $table->enum('is_admin', ['yes', 'no'])->default('no');
            $table->enum('is_supper_admin', ['yes', 'no'])->default('no');
            $table->string('ip')->nullable();
            $table->rememberToken();
            $table->string('stripe_payment_method_id', 500)->nullable();
            $table->string('stripe_customer_id', 500)->nullable();
            $table->string('message_video')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}; 