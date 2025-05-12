<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Illuminate\Support\Facades\Schema;

uses(SunhillDatabaseTestCase::class);

test('Migrate with nothing to do', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(Dummy::getExpectedStructure());
    Dummy::prepareDatabase($this);
    
    $test->migrate();
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'dummychildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'dummygrandchildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'id', 'integer');
});

test('Migrate with all missing', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(Dummy::getExpectedStructure());
    
    
    Dummy::prepareDatabase($this);
    Schema::drop('dummies');
    Schema::drop('dummychildren');
    Schema::drop('dummygrandchildren');
    
    $test->migrate();
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'dummychildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'dummygrandchildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'id', 'integer');
});

test('Migrate with grand child missing', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(Dummy::getExpectedStructure());
        
    Dummy::prepareDatabase($this);
    Schema::drop('dummygrandchildren');
    
    $test->migrate();
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'dummychildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'dummygrandchildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'id', 'integer');
});

test('Migrate with child missing', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(Dummy::getExpectedStructure());
        
    Dummy::prepareDatabase($this);
    Schema::drop('dummychildren');
    
    $test->migrate();
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'dummychildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'dummygrandchildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'id', 'integer');
});

test('Migrate with parent missing', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(Dummy::getExpectedStructure());
    
    Dummy::prepareDatabase($this);    
    Schema::drop('dummies');
    
    $test->migrate();
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'dummychildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'dummygrandchildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'id', 'integer');
});
