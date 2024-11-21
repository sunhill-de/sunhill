<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\PoolMysqlStorage\PoolMysqlStorage;
use Illuminate\Support\Facades\Schema;

uses(SunhillDatabaseTestCase::class);

test('Migrate fresh for dummy', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'dummy'));
    Schema::drop('dummies');
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableHasColumn('dummies','dummyint');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
});