<?php

use Sunhill\Tests\TestSupport\Attributes\SimpleIntAttribute;
use Sunhill\Tests\SunhillTestCase;
use Sunhill\Storage\ObjectStorage\AttributeStorage;

uses(SunhillTestCase::class);

test('test read simple int attribute', function()
{
   $storage = \Mockery::mock(AttributeStorage::class);
   $storage->shouldReceive('getValue')->with('value')->once()->andReturn(999);
   $storage->shouldReceive('load')->with(3)->once();
   $storage->shouldReceive('getIsInitialized')->with('value')->once()->andReturn(true);
   $storage->shouldReceive('setStructure');
   $test = new SimpleIntAttribute();
   $test->setStorage($storage);
   $test->load(3);
   expect($test->value)->toBe(999);
});

test('create simple int attribute', function()
{
    $storage = \Mockery::mock(AttributeStorage::class);
    $storage->shouldReceive('getIsInitialized')->with('value')->once()->andReturn(true);
    $storage->shouldReceive('setValue')->once()->with('value',123);
    $storage->shouldReceive('commit')->once();
    $storage->shouldReceive('setStructure');
    
    $test = new SimpleIntAttribute();
    $test->setStorage($storage);
    $test->value = 123;
    $test->commit();
});

test('delete simple int attribute', function()
{
    $storage = \Mockery::mock(AttributeStorage::class);
    $storage->shouldReceive('delete')->once()->with(3);
    $storage->shouldReceive('setStructure');
    
    $test = new SimpleIntAttribute();
    $test->setStorage($storage);
    $test->delete(3);
});

