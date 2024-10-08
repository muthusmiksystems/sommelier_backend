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
        Schema::create('delivery_collection_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('delivery_collection_id');
            $table->decimal('amount', 20, 2)->default(0);
            $table->string('type')->nullable();
            $table->longText('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_collection_logs');
    }
};
