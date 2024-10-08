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
        //drop delivery_pin from users table if exits...
        if (Schema::hasColumn('users', 'delivery_pin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('delivery_pin');
            });
        }

        //add delivery_pin to orders tabel
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_pin')->default('123456')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
