<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagar_pix_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('payment_id');
            $table->string('status');
            $table->double('amount', 8, 2);
            $table->string('transaction_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagar_pix_payments');
    }
}; 