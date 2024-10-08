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
            $table->string('max_cover_breakfast')->nullable();
            $table->string('max_cover_lunch')->nullable();
            $table->string('max_cover_dinner')->nullable();
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
