<?php

namespace Sunhill\Tests\Unit\Storage\AbstractObjectStorage;

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Facades\Properties;

uses(SunhillTestCase::class);

function getTestStorage()
{
    Properties::shouldReceive('getAttributeID')->with('attribute1')->andReturn(1);
    Properties::shouldReceive('getAttributeID')->with('attribute3')->andReturn(3);
    Properties::shouldReceive('storeAttribute')->with(1,5,'newvalue1');
    Properties::shouldReceive('storeAttribute')->with(3,5,'newvalue3');
    $test = new DummyAbstractObjectStorage();
    $test::$DataPool = $test::$Data;
    $test->setStructure(ChildObject::getExpectedStructure());
    $test->setValue('_uuid', '25c078e8-7614-41d7-9eb8-aeee906456c6');
    $test->setValue('_classname', 'ChildObject');
    $test->setValue('_read_cap',null);
    $test->setValue('_modify_cap',null);
    $test->setValue('_delete_cap',null);
    $test->setValue('_created_at','2025-04-19 21:29:00');
    $test->setValue('_updated_at','2025-04-19 21:29:00');    
    $test->setValue('parent_int',666);
    $test->setValue('parent_string','NEW');
    $test->setIndexedValue('parent_sarray',0,1111);
    $test->setIndexedValue('parent_sarray',1,2222);
    $test->setValue('child_int',777);
    $test->setValue('child_string','WEN');
    $test->setIndexedValue('child_sarray',0,1221);
    $test->setIndexedValue('child_sarray',1,2332);
    $test->setIndexedValue('_tags',0,1);
    $test->setIndexedValue('_tags',1,2);
    $test->setIndexedValue('_attributes','attribute1','newvalue1');
    $test->setIndexedValue('_attributes','attribute3','newvalue3');
    
    $test->commit();
    
    return $test;
}

test('Create a ChildObject creates the object', function()
{
    $test = getTestStorage();
    
    expect($test::$DataPool['objects'][4]['id'])->toBe(5);
    expect($test::$DataPool['objects'][4]['_classname'])->toBe('ChildObject');
    expect($test::$DataPool['objects'][4]['_uuid'])->toBe('25c078e8-7614-41d7-9eb8-aeee906456c6');
});

test('Create a ChildObject creates the ParentObject simple field', function()
{
    $test = getTestStorage();
    
    expect($test::$DataPool['parentobjects'][4]['id'])->toBe(5);
    expect($test::$DataPool['parentobjects'][4]['parent_int'])->toBe(666);
    expect($test::$DataPool['parentobjects'][4]['parent_string'])->toBe('NEW');
});

test('Create a ChildObject creates the ParentObject array', function()
{
    $test = getTestStorage();
    
    expect($test::$DataPool['parentobjects_parent_sarray'][6]['container_id'])->toBe(5);
    expect($test::$DataPool['parentobjects_parent_sarray'][6]['index'])->toBe(0);
    expect($test::$DataPool['parentobjects_parent_sarray'][6]['element'])->toBe(1111);
    expect($test::$DataPool['parentobjects_parent_sarray'][7]['container_id'])->toBe(5);
    expect($test::$DataPool['parentobjects_parent_sarray'][7]['index'])->toBe(1);
    expect($test::$DataPool['parentobjects_parent_sarray'][7]['element'])->toBe(2222);
});

test('Create a ChildObject creates the ChildObject simple field', function()
{
    $test = getTestStorage();
    
    expect($test::$DataPool['childobjects'][4]['id'])->toBe(5);
    expect($test::$DataPool['childobjects'][4]['child_int'])->toBe(777);
    expect($test::$DataPool['childobjects'][4]['child_string'])->toBe('WEN');
});

test('Create a ChildObject create the ChildObject array', function()
{
    $test = getTestStorage();
    
    expect($test::$DataPool['childobjects_child_sarray'][6]['container_id'])->toBe(5);
    expect($test::$DataPool['childobjects_child_sarray'][6]['index'])->toBe(0);
    expect($test::$DataPool['childobjects_child_sarray'][6]['element'])->toBe(1221);
    expect($test::$DataPool['childobjects_child_sarray'][7]['container_id'])->toBe(5);
    expect($test::$DataPool['childobjects_child_sarray'][7]['index'])->toBe(1);
    expect($test::$DataPool['childobjects_child_sarray'][7]['element'])->toBe(2332);
});

test('Create a ChildObject creates the tags', function()
{
    $test = getTestStorage();

    expect($test::$DataPool['tagobjectassigns'][5]['container_id'])->toBe(5);
    expect($test::$DataPool['tagobjectassigns'][5]['tag_id'])->toBe(1);
    expect($test::$DataPool['tagobjectassigns'][6]['container_id'])->toBe(5);
    expect($test::$DataPool['tagobjectassigns'][6]['tag_id'])->toBe(2);
    
});

test('Read a ChildObject reads the attributes', function()
{
    $test = getTestStorage();
    
    expect($test::$DataPool['attributeobjectassigns'][5]['container_id'])->toBe(5);
    expect($test::$DataPool['attributeobjectassigns'][5]['attribute_id'])->toBe(1);
    expect($test::$DataPool['attributeobjectassigns'][6]['container_id'])->toBe(5);
    expect($test::$DataPool['attributeobjectassigns'][6]['attribute_id'])->toBe(3);
});



