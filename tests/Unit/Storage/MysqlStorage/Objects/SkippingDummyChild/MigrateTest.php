<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Illuminate\Support\Facades\Schema;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;

uses(SunhillDatabaseTestCase::class);

test('Migrate with nothing to do', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(SkippingDummyChild::getExpectedStructure());
    SkippingDummyChild::prepareDatabase($this);
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('skippingdummychildren');    
    $this->assertDatabaseTableColumnIsType('skippingdummychildren', 'id', 'integer');    
})->group('migrate');

test('Migrate fresh both deleted', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(SkippingDummyChild::getExpectedStructure());
    SkippingDummyChild::prepareDatabase($this);
    Schema::drop('dummies');
    Schema::drop('skippingdummychildren');
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('skippingdummychildren');
    $this->assertDatabaseTableColumnIsType('skippingdummychildren', 'id', 'integer');
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
})->group('migrate');

test('Migrate fresh parent deleted', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(SkippingDummyChild::getExpectedStructure());
    SkippingDummyChild::prepareDatabase($this);
    Schema::drop('dummies');
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('skippingdummychildren');
    $this->assertDatabaseTableColumnIsType('skippingdummychildren', 'id', 'integer');
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
})->group('migrate');

test('Migrate fresh child deleted', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(SkippingDummyChild::getExpectedStructure());
    SkippingDummyChild::prepareDatabase($this);
    Schema::drop('skippingdummychildren');
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('skippingdummychildren');
    $this->assertDatabaseTableColumnIsType('skippingdummychildren', 'id', 'integer');
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
})->group('migrate');
