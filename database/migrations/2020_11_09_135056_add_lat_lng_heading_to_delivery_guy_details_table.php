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
        Schema::table('delivery_guy_details', function (Blueprint $table) {
            $table->string('delivery_lat')->nullable();
            $table->string('delivery_long')->nullable();
            $table->string('heading')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_guy_details', function (Blueprint $table) {
            //
        });
    }
};
