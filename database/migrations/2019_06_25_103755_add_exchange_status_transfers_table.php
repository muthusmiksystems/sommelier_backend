<?php

use Bavix\Wallet\Models\Transfer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected function table(): string
    {
        return (new Transfer())->getTable();
    }

    public function up(): void
    {
        $enums = [
            Transfer::STATUS_EXCHANGE,
            Transfer::STATUS_TRANSFER,
            Transfer::STATUS_PAID,
            Transfer::STATUS_REFUND,
            Transfer::STATUS_GIFT,
        ];

        if (DB::connection() instanceof MySqlConnection) {
            $table = $this->table();
            $enumString = implode('\', \'', $enums);
            $default = Transfer::STATUS_TRANSFER;
            DB::statement("ALTER TABLE $table CHANGE COLUMN status status ENUM('$enumString') NOT NULL DEFAULT '$default'");
            DB::statement("ALTER TABLE $table CHANGE COLUMN status_last status_last ENUM('$enumString') NULL");

            return;
        }

        if (DB::connection() instanceof PostgresConnection) {
            $this->alterEnum($this->table(), 'status', $enums);
            $this->alterEnum($this->table(), 'status_last', $enums);

            return;
        }
    }

    public function down(): void
    {
        $enums = [
            Transfer::STATUS_TRANSFER,
            Transfer::STATUS_PAID,
            Transfer::STATUS_REFUND,
            Transfer::STATUS_GIFT,
        ];

        if (DB::connection() instanceof MySqlConnection) {
            $table = $this->table();
            $enumString = implode('\', \'', $enums);
            $default = Transfer::STATUS_TRANSFER;
            DB::statement("ALTER TABLE $table CHANGE COLUMN status status ENUM('$enumString') NOT NULL DEFAULT '$default'");
            DB::statement("ALTER TABLE $table CHANGE COLUMN status_last status_last ENUM('$enumString') NULL");

            return;
        }

        if (DB::connection() instanceof PostgresConnection) {
            $this->alterEnum($this->table(), 'status', $enums);
            $this->alterEnum($this->table(), 'status_last', $enums);

            return;
        }
    }

    /**
     * Alter an enum field constraints
     */
    protected function alterEnum($table, $field, array $options): void
    {
        $check = "${table}_${field}_check";

        $enumList = [];

        foreach ($options as $option) {
            $enumList[] = sprintf("'%s'::CHARACTER VARYING", $option);
        }

        $enumString = implode(', ', $enumList);

        DB::transaction(function () use ($table, $field, $check, $enumString) {
            DB::statement(sprintf('ALTER TABLE %s DROP CONSTRAINT %s;', $table, $check));
            DB::statement(sprintf('ALTER TABLE %s ADD CONSTRAINT %s CHECK (%s::TEXT = ANY (ARRAY[%s]::TEXT[]))', $table, $check, $field, $enumString));
        });
    }
};
