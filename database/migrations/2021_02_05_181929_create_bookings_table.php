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
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('unique_booking_id');
            $table->string('booking_name');
            $table->integer('no_of_seats');
            $table->dateTime('booking_datetime');
            //$table->string('mobile_number');
            //$table->string('email');
            // $table->string('first_name');
            // $table->string('last_name');
            // $table->dateTime('dob')->nullable();
            $table->text('comments')->nullable();
            $table->string('booking_shift');
            $table->enum('booking_status', ['open', 'reserved', 'completed', 'cancelled'])->default('open');
            $table->unsignedInteger('restaurant_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();
            $table->foreign('restaurant_id')->references('id')->on('restaurants');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
