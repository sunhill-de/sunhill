<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Illuminate\Support\Facades\Schema;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Migrate with nothing to do', function()
{
    $test = prepareStorage(SkippingDummyGrandChild::class, $this);
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseHasTable('skippingdummychildren');    
    $this->assertDatabaseTableColumnIsType('skippingdummychildren', 'id', 'integer');    
    $this->assertDatabaseHasTable('skippingdummygrandchildren');
    $this->assertDatabaseTableColumnIsType('skippingdummygrandchildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('skippingdummygrandchildren', 'dummygrandchildint', 'integer');
})->group('migrate');

test('Migrate fresh both deleted', function()
{
    $test = prepareStorage(SkippingDummyGrandChild::class, $this);
    Schema::drop('dummies');
    Schema::drop('skippingdummychildren');
    Schema::drop('skippingdummygrandchildren');
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('skippingdummychildren');
    $this->assertDatabaseTableColumnIsType('skippingdummychildren', 'id', 'integer');
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseHasTable('skippingdummygrandchildren');
    $this->assertDatabaseTableColumnIsType('skippingdummygrandchildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('skippingdummygrandchildren', 'dummygrandchildint', 'integer');
})->group('migrate');

test('Migrate fresh parent deleted', function()
{
    $test = prepareStorage(SkippingDummyGrandChild::class, $this);
    Schema::drop('dummies');
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('skippingdummychildren');
    $this->assertDatabaseTableColumnIsType('skippingdummychildren', 'id', 'integer');
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseHasTable('skippingdummygrandchildren');
    $this->assertDatabaseTableColumnIsType('skippingdummygrandchildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('skippingdummygrandchildren', 'dummygrandchildint', 'integer');
})->group('migrate');

test('Migrate fresh child deleted', function()
{
    $test = prepareStorage(SkippingDummyGrandChild::class, $this);
    Schema::drop('skippingdummychildren');
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('skippingdummychildren');
    $this->assertDatabaseTableColumnIsType('skippingdummychildren', 'id', 'integer');
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseHasTable('skippingdummygrandchildren');
    $this->assertDatabaseTableColumnIsType('skippingdummygrandchildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('skippingdummygrandchildren', 'dummygrandchildint', 'integer');
})->group('migrate');

test('Migrate fresh grandchild deleted', function()
{
    $test = prepareStorage(SkippingDummyGrandChild::class, $this);
    Schema::drop('skippingdummygrandchildren');
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('skippingdummychildren');
    $this->assertDatabaseTableColumnIsType('skippingdummychildren', 'id', 'integer');
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseHasTable('skippingdummygrandchildren');
    $this->assertDatabaseTableColumnIsType('skippingdummygrandchildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('skippingdummygrandchildren', 'dummygrandchildint', 'integer');
})->group('migrate');
