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
        Schema::create('private_stream_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('private_stream_request_id');
            $table->unsignedBigInteger('user_id'); // Who gave the feedback
            $table->enum('feedback_type', ['user', 'streamer']); // Whether it's from user or streamer
            $table->integer('rating')->nullable(); // 1-5 star rating
            $table->text('comment')->nullable();
            $table->boolean('user_showed_up')->nullable(); // Did the user show up?
            $table->boolean('streamer_showed_up')->nullable(); // Did the streamer show up?
            $table->boolean('technical_issues')->default(false); // Were there technical issues?
            $table->text('technical_issues_description')->nullable();
            $table->boolean('inappropriate_behavior')->default(false); // Was there inappropriate behavior?
            $table->text('inappropriate_behavior_description')->nullable();
            $table->enum('overall_experience', ['excellent', 'good', 'average', 'poor', 'terrible'])->nullable();
            $table->boolean('would_recommend')->nullable(); // Would recommend this user/streamer?
            $table->json('additional_data')->nullable(); // For any extra feedback data
            $table->timestamps();
            
            $table->foreign('private_stream_request_id')->references('id')->on('private_stream_requests')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['private_stream_request_id', 'feedback_type'], 'feedback_stream_type_idx');
            $table->index(['user_id'], 'feedback_user_idx');
            $table->unique(['private_stream_request_id', 'user_id'], 'feedback_unique_idx'); // One feedback per user per stream
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_stream_feedbacks');
    }
}; 