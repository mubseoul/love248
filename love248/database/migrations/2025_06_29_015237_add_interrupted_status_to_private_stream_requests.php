<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'interrupted' to the status enum
        DB::statement("ALTER TABLE private_stream_requests MODIFY COLUMN status ENUM(
            'pending', 
            'accepted', 
            'rejected', 
            'in_progress', 
            'completed', 
            'cancelled', 
            'interrupted',
            'no_show', 
            'expired',
            'awaiting_feedback',
            'disputed',
            'resolved',
            'completed_with_issues',
            'streamer_no_show',
            'user_no_show'
        ) DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'interrupted' from the status enum
        DB::statement("ALTER TABLE private_stream_requests MODIFY COLUMN status ENUM(
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
        ) DEFAULT 'pending'");
    }
};
