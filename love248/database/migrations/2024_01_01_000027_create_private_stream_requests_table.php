<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('private_stream_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('streamer_id');
            $table->unsignedBigInteger('availability_id')->nullable();
            $table->date('requested_date');
            $table->time('requested_time');
            $table->integer('duration_minutes')->default(5);
            $table->decimal('room_rental_tokens', 10, 2);
            $table->decimal('streamer_fee', 10, 2);
            $table->string('currency', 10)->default('USD');
            $table->text('message')->nullable();
            $table->string('payment_method')->default('stripe');
            $table->string('payment_id')->nullable();
            $table->string('payment_status')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->string('stream_key', 64)->unique()->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('streamer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('availability_id')->references('id')->on('streamer_availability')->onDelete('set null');
            $table->index(['user_id', 'status']);
            $table->index(['streamer_id', 'status']);
            $table->index(['payment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('private_stream_requests');
    }
}; 