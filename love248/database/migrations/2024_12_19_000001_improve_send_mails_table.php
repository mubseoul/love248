<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('send_mails', function (Blueprint $table) {
            $table->string('subject')->nullable()->after('receiver_email');
            $table->integer('recipient_count')->default(0)->after('message');
            $table->string('status')->default('sent')->after('recipient_count');
        });
    }

    public function down(): void
    {
        Schema::table('send_mails', function (Blueprint $table) {
            $table->dropColumn(['subject', 'recipient_count', 'status']);
        });
    }
}; 