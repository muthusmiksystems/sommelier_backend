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
        Schema::table('shift_informations', function (Blueprint $table) {
            $table->string('breakfast_warning_covers')->nullable();
            $table->string('lunch_warning_covers')->nullable();
            $table->string('dinner_warning_covers')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_informations', function (Blueprint $table) {
            //
        });
    }
};
