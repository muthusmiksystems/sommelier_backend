<?php

use Bavix\Wallet\Models\Transaction;
use Bavix\Wallet\Models\Wallet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected function table(): string
    {
        return (new Transaction())->getTable();
    }

    protected function walletTable(): string
    {
        return (new Wallet())->getTable();
    }

    public function up(): void
    {
        Schema::table($this->table(), function (Blueprint $table) {
            $table->unsignedInteger('wallet_id')
                ->nullable()
                ->after('payable_id');

            $table->foreign('wallet_id')
                ->references('id')
                ->on($this->walletTable())
                ->onDelete('cascade');
        });

        $slug = config('wallet.wallet.default.slug', 'default');
        DB::transaction(function () use ($slug) {
            Wallet::where('slug', $slug)->each(function (Wallet $wallet) {
                Transaction::query()
                    ->where('payable_type', $wallet->holder_type)
                    ->where('payable_id', $wallet->holder_id)
                    ->update(['wallet_id' => $wallet->id]);
            });
        });
    }

    public function down(): void
    {
        Schema::table($this->table(), function (Blueprint $table) {
            if (! (DB::connection() instanceof SQLiteConnection)) {
                $table->dropForeign(['wallet_id']);
            }
            $table->dropColumn('wallet_id');
        });
    }
};
