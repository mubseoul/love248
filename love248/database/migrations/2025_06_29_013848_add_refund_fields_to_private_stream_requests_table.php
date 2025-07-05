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
            $table->decimal('refund_amount', 10, 2)->nullable()->after('tokens_awarded');
            $table->text('refund_reason')->nullable()->after('refund_amount');
            $table->timestamp('refunded_at')->nullable()->after('refund_reason');
            $table->unsignedBigInteger('refunded_by')->nullable()->after('refunded_at');
            $table->text('release_reason')->nullable()->after('refunded_by');
            
            $table->foreign('refunded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_stream_requests', function (Blueprint $table) {
            $table->dropForeign(['refunded_by']);
            $table->dropColumn([
                'refund_amount',
                'refund_reason', 
                'refunded_at',
                'refunded_by',
                'release_reason'
            ]);
        });
    }
};
