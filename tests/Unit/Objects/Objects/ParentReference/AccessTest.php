<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;
use Sunhill\Tests\TestSupport\Objects\ParentReference;

uses(SimpleTestCase::class);

test('read of Parent Reference value', function()
{
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('getValue')->with('parent_int')->andReturn(123);
    $storage->shouldReceive('getIsInitialized')->with('parent_int')->andReturn(true);    
    $storage->shouldReceive('getValue')->with('parent_reference')->andReturn(2);
    $storage->shouldReceive('getIsInitialized')->with('parent_reference')->andReturn(true);
    $storage->shouldReceive('getIndexedValue')->once()->with('parent_rarray',1)->andReturn(3);
    $storage->shouldReceive('getOffsetExists')->with('parent_rarray',1)->andReturn(true);
    $storage->shouldReceive('getIsInitialized')->with('parent_rarray')->andReturn(true);
    $test = new ParentReference();
    $test->setStorage($storage);
    
    expect($test->parent_int)->toBe(123);
    expect($test->parent_reference->getID())->toBe(2);
    expect($test->parent_rarray[1]->getID())->toBe(3);
});