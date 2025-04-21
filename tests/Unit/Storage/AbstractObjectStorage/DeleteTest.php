<?php

namespace Sunhill\Tests\Unit\Storage\AbstractObjectStorage;

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Facades\Properties;

uses(SunhillTestCase::class);

function getTestStorageForDeleting()
{
    Properties::shouldReceive('unsetAttribute')->once()->with(1,1);
    Properties::shouldReceive('unsetAttribute')->once()->with(2,1);
    $test = new DummyAbstractObjectStorage();
    $test::$DataPool = $test::$Data;
    $test->setStructure(ChildObject::getExpectedStructure());
    $test->delete(1);
    
    return $test;
}

test('Read a ChildObject reads the object', function()
{
    $test = getTestStorageForDeleting();
    
    $data = $test::getRecords('objects', 'id', 1);
    expect(empty($data))->toBe(true);
});

test('Read a ChildObject reads the ParentObject simple field', function()
{
    $test = getTestStorageForDeleting();
    
    $data = $test::getRecords('parentobjects', 'id', 1);
    expect(empty($data))->toBe(true);
});

test('Read a ChildObject reads the ParentObject array', function()
{
    $test = getTestStorageForDeleting();
    
    $data = $test::getRecords('parentobjects_parent_sarray', 'container_id', 1);
    expect(empty($data))->toBe(true);
});

test('Read a ChildObject reads the ChildObject simple field', function()
{
    $test = getTestStorageForDeleting();
    
    $data = $test::getRecords('childobjects', 'id', 1);
    expect(empty($data))->toBe(true);
});

test('Read a ChildObject reads the ChildObject array', function()
{
    $test = getTestStorageForDeleting();
    
    $data = $test::getRecords('childobjects_child_sarray', 'container_id', 1);
    expect(empty($data))->toBe(true);
});

test('Read a ChildObject reads the tags', function()
{
    $test = getTestStorageForDeleting();
    
    $data = $test::getRecords('tagobjectassigns', 'container_id', 1);
    expect(empty($data))->toBe(true);
});

test('Read a ChildObject reads the attributes', function()
{
    $test = getTestStorageForDeleting();
    
    $data = $test::getRecords('attributeobjectassigns', 'container_id', 1);
    expect(empty($data))->toBe(true);
});



