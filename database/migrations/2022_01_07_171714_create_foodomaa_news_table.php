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
        Schema::create('foodomaa_news', function (Blueprint $table) {
            $table->increments('id');
            $table->string('news_id');
            $table->text('title');
            $table->longText('content')->nullable();
            $table->text('image')->nullable();
            $table->text('link')->nullable();
            $table->boolean('is_read')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foodomaa_news');
    }
};
