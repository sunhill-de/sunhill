<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Illuminate\Support\Facades\Schema;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Migrate with nothing to do', function()
{
    $test = prepareStorage(SkippingDummyChild::class, $this);
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('skippingdummychildren');    
    $this->assertDatabaseTableColumnIsType('skippingdummychildren', 'id', 'integer');    
})->group('migrate');

test('Migrate fresh both deleted', function()
{
    $test = prepareStorage(SkippingDummyChild::class, $this);
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
    $test = prepareStorage(SkippingDummyChild::class, $this);
    Schema::drop('dummies');
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('skippingdummychildren');
    $this->assertDatabaseTableColumnIsType('skippingdummychildren', 'id', 'integer');
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
})->group('migrate');

test('Migrate fresh child deleted', function()
{
    $test = prepareStorage(SkippingDummyChild::class, $this);
    Schema::drop('skippingdummychildren');
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('skippingdummychildren');
    $this->assertDatabaseTableColumnIsType('skippingdummychildren', 'id', 'integer');
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
})->group('migrate');
