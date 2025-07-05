<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mercado_pix_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('payment_id');
            $table->string('status');
            $table->double('amount', 8, 2);
            $table->string('transaction_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mercado_pix_payments');
    }
}; 