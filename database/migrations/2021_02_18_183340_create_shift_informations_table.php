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
        Schema::create('shift_informations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('breakfastStartTime')->nullable();
            $table->string('breakfastEndTime')->nullable();
            $table->string('breakfastDuration')->nullable();
            $table->string('teafirstStartTime')->nullable();
            $table->string('teafirstEndTime')->nullable();
            $table->string('teafirstDuration')->nullable();
            $table->string('lunchStartTime')->nullable();
            $table->string('lunchEndTime')->nullable();
            $table->string('lunchDuration')->nullable();
            $table->string('teasecondStartTime')->nullable();
            $table->string('teasecondEndTime')->nullable();
            $table->string('teasecondDuration')->nullable();
            $table->string('dinnerStartTime')->nullable();
            $table->string('dinnerEndTime')->nullable();
            $table->string('dinnerDuration')->nullable();
            $table->string('maxNoOfCover')->nullable();
            $table->string('emailFrom')->nullable();
            $table->string('teamName')->nullable();
            $table->string('email_options')->nullable();
            $table->unsignedInteger('restaurant_id');
            $table->timestamps();
            $table->foreign('restaurant_id')->references('id')->on('restaurants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift_informations');
    }
};
