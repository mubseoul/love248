<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_meta', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->string('meta_key');
            $table->longText('meta_value')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_meta');
    }
}; 