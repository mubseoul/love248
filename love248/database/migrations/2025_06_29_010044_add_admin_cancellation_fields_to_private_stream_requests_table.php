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
            $table->timestamp('admin_cancelled_at')->nullable()->after('cancelled_at');
            $table->text('cancellation_reason')->nullable()->after('admin_cancelled_at');
            $table->text('interruption_reason')->nullable()->after('cancellation_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_stream_requests', function (Blueprint $table) {
            $table->dropColumn([
                'admin_cancelled_at',
                'cancellation_reason', 
                'interruption_reason'
            ]);
        });
    }
};
