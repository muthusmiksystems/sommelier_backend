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
        Schema::create('accept_deliveries', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->integer('user_id');
            $table->integer('customer_id');
            $table->boolean('is_complete')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accept_deliveries');
    }
};
