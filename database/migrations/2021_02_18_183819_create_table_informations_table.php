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
        Schema::create('table_informations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('table_number');
            $table->string('total_seats');
            $table->unsignedInteger('restaurant_id');
            $table->timestamps();
            //$table->foreign('restaurant_id')->references('id')->on('restaurants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_informations');
    }
};
