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
        Schema::table('promo_sliders', function (Blueprint $table) {
            $table->integer('position_id')->default(0);
            $table->integer('size')->default(5);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promo_sliders', function (Blueprint $table) {
            //
        });
    }
};
