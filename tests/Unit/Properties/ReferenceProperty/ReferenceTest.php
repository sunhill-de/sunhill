<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Properties\ReferenceProperty;
use Sunhill\Tests\TestSupport\Properties\DummyRecordProperty;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Properties\RecordProperty;
use Sunhill\Properties\Exceptions\InvalidValueException;
use Sunhill\Properties\PooledRecordProperty;

uses(SimpleTestCase::class);

test('Assigning a standard record works', function()
{
    $property = new DummyRecordProperty();
    
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setValue')->once()->with('container', $property);
    $storage->shouldReceive('getIsInitialized')->with('container')->andReturn(true);
    $storage->shouldReceive('getValue')->once()->with('container')->andReturn($property);
    
    $test = new ReferenceProperty();
    $test->setName('container');
    $test->setStorage($storage);   
    
    $test->setValue($property);
    expect($test->getValue())->toBe($property);
});

test('Assigning a pooled record works', function()
{
    $property = \Mockery::mock(PooledRecordProperty::class);
    $property->shouldReceive('getID')->andReturn(10);
    
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setValue')->once()->with('container', 10);
    $storage->shouldReceive('getIsInitialized')->with('container')->andReturn(true);
    $storage->shouldReceive('getValue')->once()->with('container')->andReturn(10);
    
    $test = new ReferenceProperty();
    $test->setName('container');
    $test->setStorage($storage);
    
    $test->setValue($property);
    expect($test->getValue())->toBe($property);
});

test('Loading a record from a pool works', function()
{
    $property = \Mockery::mock(PooledRecordProperty::class);
    $property->shouldReceive('getID')->andReturn(10);
        
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('getIsInitialized')->with('container')->andReturn(true);
    $storage->shouldReceive('getValue')->once()->with('container')->andReturn(10);

    $test = \Mockery::mock(ReferenceProperty::class)->makePartial()->shouldAllowMockingProtectedMethods();;
    $test->shouldReceive('tryToLoadRecord')->with(10)->andReturn($property);
    
    $test->setName('container');
    $test->setStorage($storage);
    
    expect($test->getValue())->toBe($property);
    
});

test('Test for allowed property passes', function()
{
    $property = new DummyRecordProperty();
    
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setValue')->once()->with('container', $property);
    $storage->shouldReceive('getIsInitialized')->with('container')->andReturn(true);
    
    $test = new ReferenceProperty();
    $test->setAllowedProperty(DummyRecordProperty::class);
    $test->setName('container');
    $test->setStorage($storage);
    
    $test->setValue($property);    
});

it('fails when test for allowed property fails', function()
{
    $property = new RecordProperty();
    
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('getIsInitialized')->with('container')->andReturn(true);
    
    $test = new ReferenceProperty();
    $test->setAllowedProperty(DummyRecordProperty::class);
    $test->setName('container');
    $test->setStorage($storage);
    
    $test->setValue($property);
})->throws(InvalidValueException::class);

test('Autocreate a reference', function()
{
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setValue')->once();
//    $storage->shouldReceive('getValue')->once();
    $storage->shouldReceive('getIsInitialized')->with('container')->andReturn(false);
    
    $test = new ReferenceProperty();
    $test->setAllowedProperty(DummyRecordProperty::class);
    $test->setName('container');
    $test->setStorage($storage);
    
    $test->getValue();
});

