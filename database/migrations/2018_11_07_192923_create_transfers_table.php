<?php

use Bavix\Wallet\Models\Transaction;
use Bavix\Wallet\Models\Transfer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create($this->table(), function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('from');
            $table->morphs('to');
            $table->unsignedInteger('deposit_id');
            $table->unsignedInteger('withdraw_id');
            $table->uuid('uuid')->unique();
            $table->timestamps();

            $table->foreign('deposit_id')
                ->references('id')
                ->on($this->transactionTable())
                ->onDelete('cascade');

            $table->foreign('withdraw_id')
                ->references('id')
                ->on($this->transactionTable())
                ->onDelete('cascade');
        });
    }

    protected function table(): string
    {
        return (new Transfer())->getTable();
    }

    protected function transactionTable(): string
    {
        return (new Transaction())->getTable();
    }

    public function down(): void
    {
        Schema::drop($this->table());
    }
};
