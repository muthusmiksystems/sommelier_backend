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
        Schema::create('restaurant_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url');
            $table->string('secret');
            $table->integer('till_id');
            $table->integer('operator_id');
            $table->string('offline_payment')->nullable();
            $table->string('online_payment')->nullable();
            $table->string('delivery_plu')->nullable();
            $table->string('discount_plu')->nullable();
            $table->string('surcharge_plu')->nullable();
            $table->string('tip_plu')->nullable();
            $table->string('booking_plu')->nullable();
            $table->string('table_group')->nullable();
            $table->string('account_group')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('pos_type');
            $table->enum('sommelier_online', ['yes', 'no'])->default('no');
            $table->enum('sommelier_reservations', ['yes', 'no'])->default('no');
            $table->enum('sommelier_functions', ['yes', 'no'])->default('no');
            $table->enum('somemmlier_loyalty', ['yes', 'no'])->default('no');
            $table->enum('sommelier_time_attendance', ['yes', 'no'])->default('no');
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
        Schema::dropIfExists('restaurant_settings');
    }
};
