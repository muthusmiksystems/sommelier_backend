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
        Schema::create('booking_table_information', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('table_information_id');
            $table->timestamps();

            //$table->foreign('booking_id')->references('id')->on('bookings');
            //$table->foreign('table_information_id')->references('id')->on('table_informations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_table_information');
    }
};
