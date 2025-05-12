<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Illuminate\Support\Facades\Schema;
use Sunhill\Tests\TestSupport\Objects\DummyChild;

uses(SunhillDatabaseTestCase::class);

test('Migrate with nothing to do', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(DummyChild::getExpectedStructure());
    DummyChild::prepareDatabase($this);
    
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('dummychildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'dummychildint', 'integer');
    
    $this->assertDatabaseTableColumnIsType('dummies','id','integer');
    $this->assertDatabaseTableColumnIsType('dummies','dummyint','integer');
});

test('Migrate with both not existant', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(DummyChild::getExpectedStructure());
    DummyChild::prepareDatabase($this);
    
    Schema::drop('dummies');
    Schema::drop('dummychildren');
    
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('dummychildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'dummychildint', 'integer');
    
    $this->assertDatabaseTableColumnIsType('dummies','id','integer');
    $this->assertDatabaseTableColumnIsType('dummies','dummyint','integer');
});

test('Migrate with dummy not existant', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(DummyChild::getExpectedStructure());
    DummyChild::prepareDatabase($this);
    
    Schema::drop('dummies');
    
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('dummychildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'dummychildint', 'integer');
    
    $this->assertDatabaseTableColumnIsType('dummies','id','integer');
    $this->assertDatabaseTableColumnIsType('dummies','dummyint','integer');
});

test('Migrate with dummychild not existant', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(DummyChild::getExpectedStructure());
    DummyChild::prepareDatabase($this);
    
    Schema::drop('dummychildren');
    
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('dummychildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'dummychildint', 'integer');
    
    $this->assertDatabaseTableColumnIsType('dummies','id','integer');
    $this->assertDatabaseTableColumnIsType('dummies','dummyint','integer');
});
