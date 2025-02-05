<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Tests\TestSupport\Objects\ParentReference;
use Sunhill\Properties\PooledRecordProperty;

uses(SimpleTestCase::class);

test('read of Parent Reference value', function()
{
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('getValue')->with('parent_int')->andReturn(123);
    $storage->shouldReceive('getIsInitialized')->with('parent_int')->andReturn(true);
    $test = new ParentReference();
    $test->setStorage($storage);
    // @todo Fix this unit test for referenes
/*    $storage->shouldReceive('getValue')->with('parent_reference')->andReturn(2);
    $storage->shouldReceive('getIsInitialized')->with('parent_reference')->andReturn(true);
    $storage->shouldReceive('getIndexedValue')->once()->with('parent_rarray',1)->andReturn(2);
    $storage->shouldReceive('getOffsetExists')->with('parent_rarray',1)->andReturn(true);
    $storage->shouldReceive('getIsInitialized')->with('parent_rarray')->andReturn(true);
    
    $result = \Mockery::mock(PooledRecordProperty::class);
    $result->shouldReceive('getID')->andReturn(2);
    
    //$test = new ParentReference();
    //$test->setStorage($storage);
    $test = \Mockery::mock(ParentReference::class.'[tryToLoadRecord]')->makePartial();
    $test->shouldAllowMockingProtectedMethods();
     $test->shouldReceive('tryToLoadRecord')->andReturn($result);
     $test->setStorage($storage); 
  */  
    expect($test->parent_int)->toBe(123);
/*    expect($test->parent_reference->getID())->toBe(2);
    expect($test->parent_rarray[1]->getID())->toBe(2); */
});