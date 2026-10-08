<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql' || ! Schema::hasTable('widgets')) {
            return;
        }

        $column = DB::selectOne("SHOW COLUMNS FROM `widgets` WHERE Field = 'id'");

        if ($column && ! str_contains($column->Extra, 'auto_increment')) {
            DB::statement('ALTER TABLE `widgets` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        }
    }

    public function down(): void
    {
        // Keep the auto-increment required by the original widgets schema.
    }
};
