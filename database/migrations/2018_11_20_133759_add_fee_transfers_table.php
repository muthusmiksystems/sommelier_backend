<?php

use Bavix\Wallet\Models\Transfer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function table(): string
    {
        return (new Transfer())->getTable();
    }

    public function up(): void
    {
        Schema::table($this->table(), function (Blueprint $table) {
            $table->bigInteger('fee')
                ->default(0)
                ->after('withdraw_id');
        });
    }

    public function down(): void
    {
        Schema::table($this->table(), function (Blueprint $table) {
            $table->dropColumn('fee');
        });
    }
};
