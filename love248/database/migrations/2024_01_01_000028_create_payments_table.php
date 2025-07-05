<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('type')->nullable();
            $table->string('status')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('item_type')->nullable();
            $table->string('item_id')->nullable();
            $table->longText('data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
}; 