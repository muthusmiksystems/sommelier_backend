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
        Schema::create('delivery_guy_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('age')->nullable();
            $table->string('gender')->nullable();
            $table->string('photo')->nullable();
            $table->string('description')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_guy_details');
    }
};
