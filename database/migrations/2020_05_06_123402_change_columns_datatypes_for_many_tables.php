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
        //addresses changed from longText to text
        Schema::table('addresses', function (Blueprint $table) {
            $table->text('address')->nullable()->change();
            $table->text('house')->nullable()->change();
            $table->text('landmark')->nullable()->change();
            $table->text('tag')->nullable()->change();
        });

        Schema::table('restaurants', function (Blueprint $table) {
            $table->text('slug')->nullable()->change();
            $table->text('address')->change();
            $table->text('landmark')->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->mediumText('location')->nullable()->change();
            $table->text('order_comment')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
