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
        Schema::table('slides', function (Blueprint $table) {
            $table->string('model')->nullable();
            $table->integer('item_id')->nullable();
            $table->integer('restaurant_id')->nullable();
            $table->boolean('is_locationset')->default(0);
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('radius')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slides', function (Blueprint $table) {
            //
        });
    }
};
