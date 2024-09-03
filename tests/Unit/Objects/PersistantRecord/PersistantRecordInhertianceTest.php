<?php

use Sunhill\Tests\TestCase;
use Sunhill\Types\TypeInteger;
use Sunhill\Objects\Exceptions\TypeCannotBeEmbeddedException;
use Sunhill\Tests\Unit\Objects\PersistantRecord\Samples\EmptyPersistantRecord;
use Sunhill\Facades\Properties;
use Sunhill\Properties\AbstractRecordProperty;
use Sunhill\Types\TypeVarchar;
use Sunhill\Objects\Exceptions\TypeAlreadyEmbeddedException;
use Sunhill\Properties\Exceptions\DuplicateElementNameException;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Tests\Unit\Objects\PersistantRecord\Samples\ParentRecord;
use Sunhill\Tests\Unit\Objects\PersistantRecord\Samples\ChildRecord;
use Sunhill\Tests\Unit\Objects\PersistantRecord\Samples\GrandChildRecord;
use Sunhill\Tests\Unit\Objects\PersistantRecord\Samples\EmptyChildRecord;
use Sunhill\Tests\Unit\Objects\PersistantRecord\Samples\EmptyGrandChildRecord;
use Sunhill\Properties\AbstractProperty;
use Sunhill\Objects\ObjectDescriptor;

uses(TestCase::class);

test('Parent called initializeProperties() with include', function()
{
    $descriptor = \Mockery::mock(ObjectDescriptor::class);
    $descriptor->shouldReceive('setSourceStorage')->once()->with('parentstorage');
    Properties::shouldReceive('getHirachyOfRecord')->with(ParentRecord::class)->once()->andReturn([ParentRecord::class]);
    Properties::shouldReceive('getStorageIDOfRecord')->with(ParentRecord::class)->once()->andReturn('parentstorage');    
    Properties::shouldReceive('getObjectDescriptorForRecord')->andReturn($descriptor);

    ParentRecord::$handle_inheritance = 'include';
    ParentRecord::$called_parent = 0;
    ChildRecord::$called_child = 0;
    GrandChildRecord::$called_grandchild = 0;
    $test = new ParentRecord();
    
    expect(ParentRecord::$called_parent)->toBe(1);
});

test('Child called initializeProperties() with include', function()
{
    $descriptor = \Mockery::mock(ObjectDescriptor::class);
    $descriptor->shouldReceive('setSourceStorage')->twice();
    Properties::shouldReceive('getHirachyOfRecord')->with(ChildRecord::class)->once()->andReturn([ChildRecord::class,ParentRecord::class]);
    Properties::shouldReceive('getStorageIDOfRecord')->with(ChildRecord::class)->twice()->andReturn('childstorage');
    Properties::shouldReceive('getObjectDescriptorForRecord')->andReturn($descriptor);
    
    ParentRecord::$handle_inheritance = 'include';
    ParentRecord::$called_parent = 0;
    ChildRecord::$called_child = 0;
    GrandChildRecord::$called_grandchild = 0;
    $test = new ChildRecord();
    
    expect(ParentRecord::$called_parent)->toBe(1);
    expect(ChildRecord::$called_child)->toBe(1);
});

test('EmptyChild called initializeProperties() with include', function()
{
    $descriptor = \Mockery::mock(ObjectDescriptor::class);
    $descriptor->shouldReceive('setSourceStorage')->twice()->with('emptychildstorage');
    Properties::shouldReceive('getHirachyOfRecord')->with(EmptyChildRecord::class)->once()->andReturn([EmptyChildRecord::class,ParentRecord::class]);
    Properties::shouldReceive('getStorageIDOfRecord')->with(EmptyChildRecord::class)->twice()->andReturn('emptychildstorage');
    Properties::shouldReceive('getObjectDescriptorForRecord')->andReturn($descriptor);
    
    ParentRecord::$handle_inheritance = 'include';
    ParentRecord::$called_parent = 0;
    GrandChildRecord::$called_grandchild = 0;
    $test = new EmptyChildRecord();
    
    expect(ParentRecord::$called_parent)->toBe(1);
});

test('GrandChild called initializeProperties() with include', function()
{
    $descriptor = \Mockery::mock(ObjectDescriptor::class);
    $descriptor->shouldReceive('setSourceStorage')->with('grandchildstorage');
    Properties::shouldReceive('getHirachyOfRecord')->with(GrandChildRecord::class)->once()->andReturn([GrandChildRecord::class,ChildRecord::class,ParentRecord::class]);
    Properties::shouldReceive('getStorageIDOfRecord')->with(GrandChildRecord::class)->andReturn('grandchildstorage');
    Properties::shouldReceive('getObjectDescriptorForRecord')->andReturn($descriptor);
    
    ParentRecord::$handle_inheritance = 'include';
    ParentRecord::$called_parent = 0;
    ChildRecord::$called_child = 0;
    GrandChildRecord::$called_grandchild = 0;
    $test = new GrandChildRecord();
    
    expect(ParentRecord::$called_parent)->toBe(1);
    expect(ChildRecord::$called_child)->toBe(1);
    expect(GrandChildRecord::$called_grandchild)->toBe(1);
});

test('EmptyGrandChild called initializeProperties() with include', function()
{
    $descriptor = \Mockery::mock(ObjectDescriptor::class);
    $descriptor->shouldReceive('setSourceStorage')->with('emptygrandchildstorage');
    Properties::shouldReceive('getHirachyOfRecord')->with(EmptyGrandChildRecord::class)->once()->andReturn([EmptyGrandChildRecord::class,EmptyChildRecord::class,ParentRecord::class]);
    Properties::shouldReceive('getStorageIDOfRecord')->with(EmptyGrandChildRecord::class)->andReturn('emptygrandchildstorage');
    Properties::shouldReceive('getObjectDescriptorForRecord')->andReturn($descriptor);
    
    ParentRecord::$handle_inheritance = 'include';
    ParentRecord::$called_parent = 0;
    EmptyGrandChildRecord::$called_emptygrandchild = 0;
    $test = new EmptyGrandChildRecord();
    
    expect(ParentRecord::$called_parent)->toBe(1);
    expect(EmptyGrandChildRecord::$called_emptygrandchild)->toBe(1);
});

test('Parent called initializeProperties() with embed', function()
{
    $descriptor = \Mockery::mock(ObjectDescriptor::class);
    $descriptor->shouldReceive('setSourceStorage')->once()->with('parentstorage');
    Properties::shouldReceive('getHirachyOfRecord')->with(ParentRecord::class)->once()->andReturn([ParentRecord::class]);
    Properties::shouldReceive('getStorageIDOfRecord')->with(ParentRecord::class)->once()->andReturn('parentstorage');
    Properties::shouldReceive('getObjectDescriptorForRecord')->andReturn($descriptor);
    
    ParentRecord::$handle_inheritance = 'embed';
    ParentRecord::$called_parent = 0;
    ChildRecord::$called_child = 0;
    GrandChildRecord::$called_grandchild = 0;
    $test = new ParentRecord();
    
    expect(ParentRecord::$called_parent)->toBe(1);
});

test('Child called initializeProperties() with embed', function()
{
    $descriptor = \Mockery::mock(ObjectDescriptor::class);
    $descriptor->shouldReceive('setSourceStorage')->with('childstorage')->once();
    $descriptor->shouldReceive('setSourceStorage')->with('parentstorage')->once();
    Properties::shouldReceive('getHirachyOfRecord')->with(ChildRecord::class)->once()->andReturn([ChildRecord::class,ParentRecord::class]);
    Properties::shouldReceive('getStorageIDOfRecord')->with(ChildRecord::class)->once()->andReturn('childstorage');
    Properties::shouldReceive('getStorageIDOfRecord')->with(ParentRecord::class)->once()->andReturn('parentstorage');
    Properties::shouldReceive('getObjectDescriptorForRecord')->andReturn($descriptor);
    
    ParentRecord::$handle_inheritance = 'embed';
    ParentRecord::$called_parent = 0;
    ChildRecord::$called_child = 0;
    GrandChildRecord::$called_grandchild = 0;
    $test = new ChildRecord();
    
    expect(ParentRecord::$called_parent)->toBe(1);
    expect(ChildRecord::$called_child)->toBe(1);
});

test('EmptyChild called initializeProperties() with embed', function()
{
    $descriptor = \Mockery::mock(ObjectDescriptor::class);
    $descriptor->shouldReceive('setSourceStorage')->with('emptychildstorage')->once();
    $descriptor->shouldReceive('setSourceStorage')->with('parentstorage')->once();
    Properties::shouldReceive('getHirachyOfRecord')->with(EmptyChildRecord::class)->once()->andReturn([EmptyChildRecord::class,ParentRecord::class]);
    Properties::shouldReceive('getStorageIDOfRecord')->with(EmptyChildRecord::class)->once()->andReturn('emptychildstorage');
    Properties::shouldReceive('getStorageIDOfRecord')->with(ParentRecord::class)->once()->andReturn('parentstorage');
    Properties::shouldReceive('getObjectDescriptorForRecord')->andReturn($descriptor);
    
    ParentRecord::$handle_inheritance = 'embed';
    ParentRecord::$called_parent = 0;
    GrandChildRecord::$called_grandchild = 0;
    $test = new EmptyChildRecord();
    
    expect(ParentRecord::$called_parent)->toBe(1);
});

test('GrandChild called initializeProperties() with embed', function()
{
    $descriptor = \Mockery::mock(ObjectDescriptor::class);
    $descriptor->shouldReceive('setSourceStorage')->with('childstorage')->once();
    $descriptor->shouldReceive('setSourceStorage')->with('parentstorage')->once();
    $descriptor->shouldReceive('setSourceStorage')->with('grandchildstorage')->once();
    Properties::shouldReceive('getHirachyOfRecord')->with(GrandChildRecord::class)->once()->andReturn([GrandChildRecord::class,ChildRecord::class,ParentRecord::class]);
    Properties::shouldReceive('getStorageIDOfRecord')->with(ParentRecord::class)->andReturn('parentstorage');
    Properties::shouldReceive('getStorageIDOfRecord')->with(ChildRecord::class)->andReturn('childstorage');
    Properties::shouldReceive('getStorageIDOfRecord')->with(GrandChildRecord::class)->andReturn('grandchildstorage');
    Properties::shouldReceive('getObjectDescriptorForRecord')->andReturn($descriptor);
    
    ParentRecord::$handle_inheritance = 'embed';
    ParentRecord::$called_parent = 0;
    ChildRecord::$called_child = 0;
    GrandChildRecord::$called_grandchild = 0;
    $test = new GrandChildRecord();
    
    expect(ParentRecord::$called_parent)->toBe(1);
    expect(ChildRecord::$called_child)->toBe(1);
    expect(GrandChildRecord::$called_grandchild)->toBe(1);
});

test('EmptyGrandChild called initializeProperties() with embed', function()
{
    $descriptor = \Mockery::mock(ObjectDescriptor::class);
    $descriptor->shouldReceive('setSourceStorage')->with('emptychildstorage')->once();
    $descriptor->shouldReceive('setSourceStorage')->with('parentstorage')->once();
    $descriptor->shouldReceive('setSourceStorage')->with('emptygrandchildstorage')->once();
    Properties::shouldReceive('getHirachyOfRecord')->with(EmptyGrandChildRecord::class)->once()->andReturn([EmptyGrandChildRecord::class,EmptyChildRecord::class,ParentRecord::class]);
    Properties::shouldReceive('getStorageIDOfRecord')->with(ParentRecord::class)->andReturn('parentstorage');
    Properties::shouldReceive('getStorageIDOfRecord')->with(EmptyChildRecord::class)->andReturn('emptychildstorage');
    Properties::shouldReceive('getStorageIDOfRecord')->with(EmptyGrandChildRecord::class)->andReturn('emptygrandchildstorage');
    Properties::shouldReceive('getObjectDescriptorForRecord')->andReturn($descriptor);
    
    ParentRecord::$handle_inheritance = 'embed';
    ParentRecord::$called_parent = 0;
    EmptyGrandChildRecord::$called_emptygrandchild = 0;
    $test = new EmptyGrandChildRecord();
    
    expect(ParentRecord::$called_parent)->toBe(1);
    expect(EmptyGrandChildRecord::$called_emptygrandchild)->toBe(1);
});
