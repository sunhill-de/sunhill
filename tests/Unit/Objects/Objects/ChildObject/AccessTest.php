<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Tests\TestSupport\Objects\ChildObject;

uses(SimpleTestCase::class);

test('read of Child Object value', function()
{
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('getValue')->with('parent_int')->andReturn(123);
    $storage->shouldReceive('getIsInitialized')->with('parent_int')->andReturn(true);
    $storage->shouldReceive('getValue')->with('parent_string')->andReturn('ATA');
    $storage->shouldReceive('getIsInitialized')->with('parent_string')->andReturn(true);
    $storage->shouldReceive('getIndexedValue')->once()->with('parent_sarray',1)->andReturn(2);
    $storage->shouldReceive('getOffsetExists')->with('parent_sarray',1)->andReturn(true);
    $storage->shouldReceive('getIsInitialized')->with('parent_sarray')->andReturn(true);
    
    $storage->shouldReceive('getValue')->with('child_int')->andReturn(121);
    $storage->shouldReceive('getIsInitialized')->with('child_int')->andReturn(true);
    $storage->shouldReceive('getValue')->with('child_string')->andReturn('ASA');
    $storage->shouldReceive('getIsInitialized')->with('child_string')->andReturn(true);
    $storage->shouldReceive('getIndexedValue')->once()->with('child_sarray',1)->andReturn(22);
    $storage->shouldReceive('getOffsetExists')->with('child_sarray',1)->andReturn(true);
    $storage->shouldReceive('getIsInitialized')->with('child_sarray')->andReturn(true);
    $test = new ChildObject();
    $test->setStorage($storage);
    
    expect($test->parent_int)->toBe(123);
    expect($test->parent_string)->toBe('ATA');
    expect($test->parent_sarray[1])->toBe(2);
    expect($test->child_int)->toBe(121);
    expect($test->child_string)->toBe('ASA');
    expect($test->child_sarray[1])->toBe(22);
});