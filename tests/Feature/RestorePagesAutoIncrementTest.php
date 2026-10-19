<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RestorePagesAutoIncrementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::swap(\Mockery::mock(\Illuminate\Database\Schema\Builder::class));
    }

    public function test_broken_mysql_column_is_repaired(): void
    {
        DB::shouldReceive('getDriverName')->once()->andReturn('mysql');
        Schema::shouldReceive('hasTable')->once()->with('pages')->andReturn(true);
        DB::shouldReceive('selectOne')->once()->with("SHOW COLUMNS FROM `pages` WHERE Field = 'id'")
            ->andReturn((object) ['Extra' => '']);
        DB::shouldReceive('statement')->once()
            ->with('ALTER TABLE `pages` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT')
            ->andReturn(true);

        $this->migration()->up();
        $this->assertTrue(true);
    }

    public function test_healthy_column_is_not_altered_again(): void
    {
        DB::shouldReceive('getDriverName')->once()->andReturn('mysql');
        Schema::shouldReceive('hasTable')->once()->with('pages')->andReturn(true);
        DB::shouldReceive('selectOne')->once()->andReturn((object) ['Extra' => 'auto_increment']);
        DB::shouldReceive('statement')->never();

        $this->migration()->up();
        $this->assertTrue(true);
    }

    public function test_missing_table_is_skipped(): void
    {
        DB::shouldReceive('getDriverName')->once()->andReturn('mysql');
        Schema::shouldReceive('hasTable')->once()->with('pages')->andReturn(false);
        DB::shouldReceive('selectOne')->never();
        DB::shouldReceive('statement')->never();

        $this->migration()->up();
        $this->assertTrue(true);
    }

    public function test_other_database_drivers_are_not_given_mysql_sql(): void
    {
        DB::shouldReceive('getDriverName')->once()->andReturn('sqlite');
        Schema::shouldReceive('hasTable')->never();
        DB::shouldReceive('selectOne')->never();
        DB::shouldReceive('statement')->never();

        $this->migration()->up();
        $this->assertTrue(true);
    }

    public function test_rollback_does_not_restore_the_broken_schema(): void
    {
        DB::shouldReceive('statement')->never();
        $this->migration()->down();
        $this->assertTrue(true);
    }

    private function migration(): \Illuminate\Database\Migrations\Migration
    {
        return require database_path('migrations/2026_10_12_150000_restore_pages_auto_increment.php');
    }
}
