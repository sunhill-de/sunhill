<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Properties\ArrayProperty;
use Sunhill\Properties\ReferenceProperty;
use Sunhill\Tests\TestSupport\Properties\DummyRecordProperty;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Properties\PooledRecordProperty;
use Sunhill\Properties\ReferenceArrayProperty;

uses(SunhillTestCase::class);

test('Assigning standard record works', function()
{
    $record = new DummyRecordProperty();

    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setIndexedValue')->once()->with('test', null, $record);
    $storage->shouldReceive('getIsInitialized')->with('test')->andReturn(true);
    $storage->shouldReceive('getOffsetExists')->with('test',0)->andReturn(true);
    $storage->shouldReceive('getElementCount')->once()->with('test')->andReturn(0);
    
    $test = new ReferenceArrayProperty();
    $test->setAllowedElementType(ReferenceProperty::class);
    $test->setName('test');
    $test->setStorage($storage);
    
    $test[] = $record;
    expect($test[0])->toBe($record);
});

test('Assigning a pooled record works', function()
{
    $property = \Mockery::mock(PooledRecordProperty::class);
    $property->shouldReceive('getID')->andReturn(10);
    
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setIndexedValue')->once()->with('test', 0, 10);
    $storage->shouldReceive('getIsInitialized')->with('test')->andReturn(true);
    $storage->shouldReceive('getOffsetExists')->with('test',0)->andReturn(true);
    $storage->shouldReceive('getElementCount')->once()->with('test')->andReturn(0);
    
    $test = new ReferenceArrayProperty();
    $test->setAllowedElementType(ReferenceProperty::class);
    $test->setName('test');
    $test->setStorage($storage);
    
    $test[] = $property;
    expect($test[0])->toBe($property);    
});

test('Loading a record from a pool works', function()
{
    $property = \Mockery::mock(PooledRecordProperty::class);
    $property->shouldReceive('getID')->andReturn(10);
    
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('getIsInitialized')->with('test')->andReturn(true);
    $storage->shouldReceive('getOffsetExists')->with('test',0)->andReturn(true);
    $storage->shouldReceive('getIndexedValue')->once()->with('test',0)->andReturn(10);
    
    $test = \Mockery::mock(ReferenceArrayProperty::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $test->shouldReceive('tryToLoadRecord')->with(10)->andReturn($property);
    
    $test->setName('test');
    $test->setStorage($storage);
    
    expect($test[0])->toBe($property);    
});

test('Assigning a whole array works', function()
{
    $property1 = \Mockery::mock(PooledRecordProperty::class);
    $property1->shouldReceive('getID')->andReturn(10);
    $property2 = \Mockery::mock(PooledRecordProperty::class);
    $property2->shouldReceive('getID')->andReturn(11);
    
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('getIsInitialized')->with('test')->andReturn(true);
    $storage->shouldReceive('setIndexedValue')->once()->with('test',0,10);
    $storage->shouldReceive('setIndexedValue')->once()->with('test',1,11);
    
    $test = new ReferenceArrayProperty();
    $test->setName('test');
    $test->setStorage($storage);
    
    $test->setValue([$property1,$property2]);
});

