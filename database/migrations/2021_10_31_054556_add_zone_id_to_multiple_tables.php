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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('zone_id')->nullable();
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->integer('zone_id')->nullable();
        });

        Schema::table('items', function (Blueprint $table) {
            $table->integer('zone_id')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->integer('zone_id')->nullable();
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->integer('zone_id')->nullable();
        });

        Schema::table('delivery_collections', function (Blueprint $table) {
            $table->integer('zone_id')->nullable();
        });

        Schema::table('delivery_collection_logs', function (Blueprint $table) {
            $table->integer('zone_id')->nullable();
        });

        Schema::table('restaurant_earnings', function (Blueprint $table) {
            $table->integer('zone_id')->nullable();
        });

        Schema::table('restaurant_payouts', function (Blueprint $table) {
            $table->integer('zone_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('multiple_tables', function (Blueprint $table) {
            //
        });
    }
};
