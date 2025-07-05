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
        Schema::create('bank_customers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('customer');
            $table->string('name')->nullable();
            $table->string('document');
            $table->string('document_type');
            $table->string('type');
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
        Schema::dropIfExists('bank_customers');
    }
}; 