<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Illuminate\Support\Facades\Schema;

uses(SunhillDatabaseTestCase::class);

test('Migrate fresh for dummy', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(Dummy::getExpectedStructure());
    Dummy::prepareDatabase($this);
    
    Schema::drop('dummies');
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableHasColumn('dummies','dummyint');
    $this->assertDatabaseTableHasColumn('dummies','id');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
});

