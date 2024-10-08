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
            $table->integer('vehicle_type')->nullable();
            $table->string('registration_no')->nullable();
            $table->string('abn_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bsb')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('vehicle_registration')->nullable();
            $table->string('vehicle_insurance_policy')->nullable();
            $table->string('certificate')->nullable();
            $table->string('police_clearence_certificate')->nullable();
            $table->string('address')->nullable();
            $table->string('suburb')->nullable();
            $table->string('zip')->nullable();
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
