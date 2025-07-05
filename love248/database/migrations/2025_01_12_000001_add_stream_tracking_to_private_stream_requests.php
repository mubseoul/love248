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
        Schema::table('private_stream_requests', function (Blueprint $table) {
            // Stream session tracking
            $table->timestamp('actual_start_time')->nullable()->after('accepted_at');
            $table->timestamp('countdown_started_at')->nullable()->after('actual_start_time');
            $table->timestamp('user_joined_at')->nullable()->after('countdown_started_at');
            $table->timestamp('stream_ended_at')->nullable()->after('user_joined_at');
            $table->boolean('user_joined')->default(false)->after('stream_ended_at');
            $table->integer('actual_duration_minutes')->nullable()->after('user_joined');
            
            // Feedback and dispute tracking
            $table->boolean('requires_feedback')->default(false)->after('actual_duration_minutes');
            $table->boolean('streamer_feedback_given')->default(false)->after('requires_feedback');
            $table->boolean('user_feedback_given')->default(false)->after('streamer_feedback_given');
            $table->boolean('has_dispute')->default(false)->after('user_feedback_given');
            $table->timestamp('dispute_created_at')->nullable()->after('has_dispute');
            $table->unsignedBigInteger('dispute_resolved_by')->nullable()->after('dispute_created_at');
            $table->timestamp('dispute_resolved_at')->nullable()->after('dispute_resolved_by');
            
            // Payment release tracking
            $table->timestamp('released_at')->nullable()->after('dispute_resolved_at');
            $table->unsignedBigInteger('released_by')->nullable()->after('released_at');
            $table->decimal('tokens_awarded', 10, 2)->nullable()->after('released_by');
            
            // Update status enum to include new states
            $table->dropColumn('status');
        });
        
        // Add the new status enum with additional states
        Schema::table('private_stream_requests', function (Blueprint $table) {
            $table->enum('status', [
                'pending', 
                'accepted', 
                'rejected', 
                'in_progress', 
                'completed', 
                'cancelled', 
                'no_show', 
                'expired',
                'awaiting_feedback',
                'disputed',
                'resolved',
                'completed_with_issues',
                'streamer_no_show',
                'user_no_show'
            ])->default('pending')->after('payment_status');
            
            $table->foreign('dispute_resolved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('released_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['status', 'requires_feedback']);
            $table->index(['has_dispute']);
            
            // Add index for faster duplicate checking (unique constraint handled in backend)
            $table->index(['user_id', 'streamer_id', 'requested_date', 'requested_time', 'status'], 'idx_request_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_stream_requests', function (Blueprint $table) {
            $table->dropForeign(['dispute_resolved_by']);
            $table->dropForeign(['released_by']);
            $table->dropIndex(['status', 'requires_feedback']);
            $table->dropIndex(['has_dispute']);
            $table->dropIndex('idx_request_lookup');
            
            $table->dropColumn([
                'actual_start_time',
                'countdown_started_at', 
                'user_joined_at',
                'stream_ended_at',
                'user_joined',
                'actual_duration_minutes',
                'requires_feedback',
                'streamer_feedback_given',
                'user_feedback_given',
                'has_dispute',
                'dispute_created_at',
                'dispute_resolved_by',
                'dispute_resolved_at',
                'released_at',
                'released_by',
                'tokens_awarded',
                'status'
            ]);
        });
        
        // Restore original status enum
        Schema::table('private_stream_requests', function (Blueprint $table) {
            $table->enum('status', ['pending', 'accepted', 'rejected', 'completed', 'cancelled', 'no_show', 'expired'])->default('pending');
        });
    }
}; 