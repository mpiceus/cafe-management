<?php

namespace Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected bool $useTransactions = true;

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->useTransactions) {
            DB::beginTransaction();
        }
    }

    protected function tearDown(): void
    {
        if ($this->useTransactions && DB::transactionLevel() > 0) {
            DB::rollBack();
        }

        parent::tearDown();
    }

    protected function requireTables(array $tables): void
    {
        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                $this->markTestSkipped('Missing table: '.$table);
            }
        }
    }
}
