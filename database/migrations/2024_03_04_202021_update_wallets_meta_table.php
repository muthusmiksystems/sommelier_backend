<?php

declare(strict_types=1);

use Bavix\Wallet\Internal\Service\UuidFactoryServiceInterface;
use Bavix\Wallet\Models\Wallet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn($this->table(), 'meta')) {
            return;
        }

        // upgrade from 6.x
        Schema::table($this->table(), static function (Blueprint $table) {
            $table->json('meta')
                ->after('slug')
                ->nullable()
            ;
        });
    }

    public function down(): void
    {
        Schema::dropColumns($this->table(), ['meta']);
    }

    private function table(): string
    {
        return (new Wallet())->getTable();
    }
};
