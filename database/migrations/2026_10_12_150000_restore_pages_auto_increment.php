<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! in_array(DB::getDriverName(), ['mysql', 'mariadb'], true) || ! Schema::hasTable('pages')) {
            return;
        }

        $column = DB::selectOne("SHOW COLUMNS FROM `pages` WHERE Field = 'id'");

        if ($column && ! str_contains($column->Extra, 'auto_increment')) {
            DB::statement('ALTER TABLE `pages` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');
        }
    }

    public function down(): void
    {
        // Keep the auto-increment required by the original pages schema.
    }
};
