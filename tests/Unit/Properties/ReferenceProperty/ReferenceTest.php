<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Properties\ReferenceProperty;
use Sunhill\Tests\TestSupport\Properties\DummyRecordProperty;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Properties\RecordProperty;
use Sunhill\Properties\Exceptions\InvalidValueException;

uses(SimpleTestCase::class);

test('Assigning an record works', function()
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
    $test->getValue();
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