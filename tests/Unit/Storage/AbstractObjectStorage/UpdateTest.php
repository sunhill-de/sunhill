<?php

namespace Sunhill\Tests\Unit\Storage\AbstractObjectStorage;

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Facades\Properties;

uses(SunhillTestCase::class);

function getObjectStorageForUpdate()
{
    Properties::shouldReceive('loadAttribute')->with(1,1)->andReturn(makeStdclass(['name'=>'attribute1','type'=>'string','value'=>'value1']));
    Properties::shouldReceive('loadAttribute')->with(2,1)->andReturn(makeStdclass(['name'=>'attribute2','type'=>'string','value'=>'value2']));
    $test = new DummyAbstractObjectStorage();
    $test::$DataPool = $test::$Data;
    $test->setStructure(ChildObject::getExpectedStructure());
    $test->load(1);
    
    return $test;
}

test('Update a ChildObject updates the object', function()
{
    $test = getObjectStorageForUpdate();
    
    $test->setValue('_updated_at','2025-04-20 20:27:00');
    
    $test->commit();
    
    $data = $test::getRecords('objects', 'id', 1);
    
    expect($data[0]['id'])->toBe(1);
    expect($data[0]['_classname'])->toBe('ChildObject');
    expect($data[0]['_updated_at'])->toBe('2025-04-20 20:27:00');
});

test('Update a ChildObject updates the ParentObject simple field', function()
{
    $test = getObjectStorageForUpdate();
    
    $test->setValue('parent_int',600);
    $test->setValue('parent_string','NAA');
    
    $test->commit();

    $data = $test::getRecords('parentobjects','id',1);
    expect($data[0]['parent_int'])->toBe(600);
    expect($data[0]['parent_string'])->toBe('NAA');
    
});

test('Update a ChildObject updates the ParentObject array (change single value)', function()
{
    $test = getObjectStorageForUpdate();
    
    $test->setIndexedValue('parent_sarray',1,600);
    
    $test->commit();
    
    $data = $test::getRecords('parentobjects_parent_sarray', 'container_id', 1);
    expect($data[0]['container_id'])->toBe(1);
    expect($data[0]['index'])->toBe(0);
    expect($data[0]['element'])->toBe(321);
    expect($data[1]['container_id'])->toBe(1);
    expect($data[1]['index'])->toBe(1);
    expect($data[1]['element'])->toBe(600);
    expect($data[2]['container_id'])->toBe(1);
    expect($data[2]['index'])->toBe(2);
    expect($data[2]['element'])->toBe(543);
});

test('Update a ChildObject updates the ParentObject array (delete single value)', function()
{
    $test = getObjectStorageForUpdate();
    
    $test->unsetIndexedValue('parent_sarray',1);
    
    $test->commit();
    
    $data = $test::getRecords('parentobjects_parent_sarray', 'container_id', 1);
    expect($data[0]['container_id'])->toBe(1);
    expect($data[0]['index'])->toBe(0);
    expect($data[0]['element'])->toBe(321);
    expect($data[1]['container_id'])->toBe(1);
    expect($data[1]['index'])->toBe(1);
    expect($data[1]['element'])->toBe(543);
});

test('Update a ChildObject updates the ParentObject array (append single value)', function()
{
    $test = getObjectStorageForUpdate();
    
    $test->setIndexedValue('parent_sarray',null,654);
    
    $test->commit();
    
    $data = $test::getRecords('parentobjects_parent_sarray', 'container_id', 1);
    expect($data[0]['container_id'])->toBe(1);
    expect($data[0]['index'])->toBe(0);
    expect($data[0]['element'])->toBe(321);
    expect($data[1]['container_id'])->toBe(1);
    expect($data[1]['index'])->toBe(1);
    expect($data[1]['element'])->toBe(432);
    expect($data[2]['container_id'])->toBe(1);
    expect($data[2]['index'])->toBe(2);
    expect($data[2]['element'])->toBe(543);
    expect($data[3]['container_id'])->toBe(1);
    expect($data[3]['index'])->toBe(3);
    expect($data[3]['element'])->toBe(654);
});

test('Update a ChildObject updates the ParentObject array (clear array)', function()
{
    $test = getObjectStorageForUpdate();
    
    $test->clearArray('parent_sarray');
    
    $test->commit();
    
    expect(empty($test::getRecords('parentobjects_parent_sarray', 'container_id', 1)))->toBe(true);
});

test('Update a ChildObject updates the ChildObject simple field', function()
{
    $test = getObjectStorageForUpdate();
    
    $test->setValue('child_int',600);
    $test->setValue('child_string','NAA');
    
    $test->commit();
    
    $data = $test::getRecords('childobjects','id',1);
    expect($data[0]['child_int'])->toBe(600);
    expect($data[0]['child_string'])->toBe('NAA');
});

test('Update a ChildObject updates the ChildObject array (change single value)', function()
{
    $test = getObjectStorageForUpdate();
    
    $test->setIndexedValue('child_sarray',1,600);
    
    $test->commit();
    
    $data = $test::getRecords('childobjects_child_sarray', 'container_id', 1);
    expect($data[0]['container_id'])->toBe(1);
    expect($data[0]['index'])->toBe(0);
    expect($data[0]['element'])->toBe(322);
    expect($data[1]['container_id'])->toBe(1);
    expect($data[1]['index'])->toBe(1);
    expect($data[1]['element'])->toBe(600);
    expect($data[2]['container_id'])->toBe(1);
    expect($data[2]['index'])->toBe(2);
    expect($data[2]['element'])->toBe(544);
});

test('Update a ChildObject updates the ChildObject array (delete single value)', function()
{
    $test = getObjectStorageForUpdate();
    
    $test->unsetIndexedValue('child_sarray',1);
    
    $test->commit();
    
    $data = $test::getRecords('childobjects_child_sarray', 'container_id', 1);
    expect($data[0]['container_id'])->toBe(1);
    expect($data[0]['index'])->toBe(0);
    expect($data[0]['element'])->toBe(322);
    expect($data[1]['container_id'])->toBe(1);
    expect($data[1]['index'])->toBe(1);
    expect($data[1]['element'])->toBe(544);
});

test('Update a ChildObject updates the ChildObject array (append single value)', function()
{
    $test = getObjectStorageForUpdate();
    
    $test->setIndexedValue('child_sarray',null,654);
    
    $test->commit();
    
    $data = $test::getRecords('childobjects_child_sarray', 'container_id', 1);
    expect($data[0]['container_id'])->toBe(1);
    expect($data[0]['index'])->toBe(0);
    expect($data[0]['element'])->toBe(322);
    expect($data[1]['container_id'])->toBe(1);
    expect($data[1]['index'])->toBe(1);
    expect($data[1]['element'])->toBe(433);
    expect($data[2]['container_id'])->toBe(1);
    expect($data[2]['index'])->toBe(2);
    expect($data[2]['element'])->toBe(544);
    expect($data[3]['container_id'])->toBe(1);
    expect($data[3]['index'])->toBe(3);
    expect($data[3]['element'])->toBe(654);
});

test('Update a ChildObject updates the ChildObject array (clear array)', function()
{
    $test = getObjectStorageForUpdate();
    
    $test->clearArray('child_sarray');
    
    $test->commit();
    
    expect(empty($test::getRecords('childobjects_child_sarray', 'container_id', 1)))->toBe(true);
});

test('Update a ChildObject updates the tags (change one tag)', function()
{
    $test = getObjectStorageForUpdate();
    
    $test->setIndexedValue('_tags',1,4);
    
    $test->commit();
    
    $data = $test::getRecords('tagobjectassigns','container_id',1);
    
    expect($data[0]['container_id'])->toBe(1);
    expect($data[1]['container_id'])->toBe(1);
    expect($data[0]['tag_id'])->toBe(1);
    expect($data[1]['tag_id'])->toBe(4);    
});

test('Update a ChildObject updates the tags (append one tag)', function()
{
    $test = getObjectStorageForUpdate();
    
    $test->setIndexedValue('_tags',null,4);
    
    $test->commit();
    
    $data = $test::getRecords('tagobjectassigns','container_id',1);
    
    expect($data[0]['container_id'])->toBe(1);
    expect($data[1]['container_id'])->toBe(1);
    expect($data[2]['container_id'])->toBe(1);
    expect($data[0]['tag_id'])->toBe(1);
    expect($data[1]['tag_id'])->toBe(2);
    expect($data[2]['tag_id'])->toBe(4);
});

test('Update a ChildObject updates the tags (remove one tag)', function()
{
    $test = getObjectStorageForUpdate();
    
    $test->unsetIndexedValue('_tags',0);
    
    $test->commit();
    
    $data = $test::getRecords('tagobjectassigns','container_id',1);
    
    expect($data[0]['container_id'])->toBe(1);
    expect($data[0]['tag_id'])->toBe(2);
});

test('Update a ChildObject updates the tags (clear tag)', function()
{
    $test = getObjectStorageForUpdate();
    
    $test->clearArray('_tags');
    
    $test->commit();
    
    expect(empty($test::getRecords('tagobjectassigns','container_id',1)))->toBe(true);    
});

test('Update a ChildObject updates the attributes (change one attribute)', function()
{
    Properties::shouldReceive('getAttributeID')->with('attribute1')->andReturn(1);
    Properties::shouldReceive('getAttributeID')->with('attribute2')->andReturn(2);
    Properties::shouldReceive('storeAttribute')->once()->with(1,1,'changedvalue1');
    Properties::shouldReceive('storeAttribute')->with(2,1,'value2');
    
    $test = getObjectStorageForUpdate();
    
    $test->setIndexedValue('_attributes', 'attribute1', 'changedvalue1');
    
    $test->commit();
    
    $data = $test::getRecords('attributeobjectassigns', 'container_id', 1);
    expect($data[0]['container_id'])->toBe(1);
    expect($data[0]['attribute_id'])->toBe(1);
    expect($data[1]['container_id'])->toBe(1);
    expect($data[1]['attribute_id'])->toBe(2);    
});


test('Update a ChildObject updates the attributes (add one attribute)', function()
{
    Properties::shouldReceive('getAttributeID')->with('attribute1')->andReturn(1);
    Properties::shouldReceive('getAttributeID')->with('attribute2')->andReturn(2);
    Properties::shouldReceive('getAttributeID')->with('attribute3')->andReturn(3);
    Properties::shouldReceive('storeAttribute')->with(1,1,'value1');
    Properties::shouldReceive('storeAttribute')->with(2,1,'value2');
    Properties::shouldReceive('storeAttribute')->with(3,1,'newvalue3');
    
    $test = getObjectStorageForUpdate();
    
    $test->setIndexedValue('_attributes', 'attribute3', 'newvalue3');
    
    $test->commit();
    
    $data = $test::getRecords('attributeobjectassigns', 'container_id', 1);
    expect($data[0]['container_id'])->toBe(1);
    expect($data[0]['attribute_id'])->toBe(1);
    expect($data[1]['container_id'])->toBe(1);
    expect($data[1]['attribute_id'])->toBe(2);
    expect($data[2]['container_id'])->toBe(1);
    expect($data[2]['attribute_id'])->toBe(3);
});

test('Update a ChildObject updates the attributes (remove one attribute)', function()
{
    Properties::shouldReceive('getAttributeID')->with('attribute1')->andReturn(1);
    Properties::shouldReceive('getAttributeID')->with('attribute2')->andReturn(2);
    Properties::shouldReceive('removeAttribute')->with(1,1);
    Properties::shouldReceive('storeAttribute')->with(2,1,'value2');
    
    $test = getObjectStorageForUpdate();
    
    $test->unsetIndexedValue('_attributes', 'attribute1');
    
    $test->commit();
    
    $data = $test::getRecords('attributeobjectassigns', 'container_id', 1);
    expect($data[0]['container_id'])->toBe(1);
    expect($data[0]['attribute_id'])->toBe(2);
});


