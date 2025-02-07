<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SunhillDatabaseTestCase::class);

test('Update a parentobject with modified array (all entries) and modified simple fields', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    setProtectedProperty($test, 'id', 7);
    
    $test->setValue('parent_int',111);
    $test->setValue('parent_string','AAA');
    $test->setValue('parent_sarray',[10,11,12]);
    
    $test->setValue('parent_int',919);
    $test->setValue('parent_string','AZA');
    $test->setValue('parent_sarray',[100,110,120]);
    
    $test->setValue('_classname','Dummy');
    $test->setValue('_uuid','11b47be8-05f1-4f7b-8a97-e1e6488dbd44');
    $test->setValue('_read_cap', null);
    $test->setValue('_write_cap', null);
    $test->setValue('_modify_cap', null);
    $test->setValue('_delete_cap', null);
    $test->setValue('_created_at', '2024-11-14 20:00:00');
    $test->setValue('_updated_at', '2024-11-14 20:00:00');
    $test->setValue('_tags',[]);
    $test->setValue('_attributes',[]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>7,'parent_int'=>919,'parent_string'=>'AZA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>0,'element'=>100]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>1,'element'=>110]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>2,'element'=>120]);
});

test('Update a parentobject with modified array (added a entry) and unmodified simple fields', function()
{
    
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    setProtectedProperty($test, 'id', 7);
    
    $test->setValue('parent_int',111);
    $test->setValue('parent_string','AAA');
    $test->setValue('parent_sarray',[10,11,12]);
    
    $test->setValue('parent_sarray',[10,11,12,99]);
    
    $test->setValue('_classname','Dummy');
    $test->setValue('_uuid','11b47be8-05f1-4f7b-8a97-e1e6488dbd44');
    $test->setValue('_read_cap', null);
    $test->setValue('_write_cap', null);
    $test->setValue('_modify_cap', null);
    $test->setValue('_delete_cap', null);
    $test->setValue('_created_at', '2024-11-14 20:00:00');
    $test->setValue('_updated_at', '2024-11-14 20:00:00');
    $test->setValue('_tags',[]);
    $test->setValue('_attributes',[]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>7,'parent_int'=>111,'parent_string'=>'AAA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>0,'element'=>10]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>1,'element'=>11]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>2,'element'=>12]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>3,'element'=>99]);
});

test('Update a parentobject with modified array (deleted a entry) and unmodified simple fields', function()
{
    
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    setProtectedProperty($test, 'id', 7);
    
    $test->setValue('parent_int',111);
    $test->setValue('parent_string','AAA');
    $test->setValue('parent_sarray',[10,11,12]);
    
    $test->setValue('parent_sarray',[10,11]);
    
    $test->setValue('_classname','Dummy');
    $test->setValue('_uuid','11b47be8-05f1-4f7b-8a97-e1e6488dbd44');
    $test->setValue('_read_cap', null);
    $test->setValue('_write_cap', null);
    $test->setValue('_modify_cap', null);
    $test->setValue('_delete_cap', null);
    $test->setValue('_created_at', '2024-11-14 20:00:00');
    $test->setValue('_updated_at', '2024-11-14 20:00:00');
    $test->setValue('_tags',[]);
    $test->setValue('_attributes',[]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>7,'parent_int'=>111,'parent_string'=>'AAA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>0,'element'=>10]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>1,'element'=>11]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>7,'index'=>2]);
});

test('Update a parentobject with modified array (deleted all entries) and unmodified simple fields', function()
{
    
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    setProtectedProperty($test, 'id', 7);
    
    $test->setValue('parent_int',111);
    $test->setValue('parent_string','AAA');
    $test->setValue('parent_sarray',[10,11,12]);
    
    $test->setValue('parent_sarray',[]);
    
    $test->setValue('_classname','Dummy');
    $test->setValue('_uuid','11b47be8-05f1-4f7b-8a97-e1e6488dbd44');
    $test->setValue('_read_cap', null);
    $test->setValue('_write_cap', null);
    $test->setValue('_modify_cap', null);
    $test->setValue('_delete_cap', null);
    $test->setValue('_created_at', '2024-11-14 20:00:00');
    $test->setValue('_updated_at', '2024-11-14 20:00:00');
    $test->setValue('_tags',[]);
    $test->setValue('_attributes',[]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>7,'parent_int'=>111,'parent_string'=>'AAA']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>7]);
});

test('Update a parentobject with modified array (previously empty array) and unmodified simple fields', function()
{
    
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    setProtectedProperty($test, 'id', 8);
    
    $test->setValue('parent_int',222);
    $test->setValue('parent_string','BBB');
    $test->setValue('parent_sarray',[]);
    
    $test->setValue('parent_sarray',[110,111]);
    
    $test->setValue('_classname','Dummy');
    $test->setValue('_uuid','11b47be8-05f1-4f7b-8a97-e1e6488dbd44');
    $test->setValue('_read_cap', null);
    $test->setValue('_write_cap', null);
    $test->setValue('_modify_cap', null);
    $test->setValue('_delete_cap', null);
    $test->setValue('_created_at', '2024-11-14 20:00:00');
    $test->setValue('_updated_at', '2024-11-14 20:00:00');
    $test->setValue('_tags',[]);
    $test->setValue('_attributes',[]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>8,'parent_int'=>222,'parent_string'=>'BBB']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>8,'index'=>0,'element'=>110]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>8,'index'=>1,'element'=>111]);
});

test('Update a parentobject with unmodified array (empty) and modified simple fields', function()
{
    
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    setProtectedProperty($test, 'id', 8);
    
    $test->setValue('parent_int',222);
    $test->setValue('parent_string','BBB');
    $test->setValue('parent_sarray',[]);
    
    $test->setValue('parent_int',2222);
    $test->setValue('parent_string','BBBB');
    
    $test->setValue('_classname','Dummy');
    $test->setValue('_uuid','11b47be8-05f1-4f7b-8a97-e1e6488dbd44');
    $test->setValue('_read_cap', null);
    $test->setValue('_write_cap', null);
    $test->setValue('_modify_cap', null);
    $test->setValue('_delete_cap', null);
    $test->setValue('_created_at', '2024-11-14 20:00:00');
    $test->setValue('_updated_at', '2024-11-14 20:00:00');
    $test->setValue('_tags',[]);
    $test->setValue('_attributes',[]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>8,'parent_int'=>2222,'parent_string'=>'BBBB']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>8]);
});

test('Update a parentobject with unmodified array (had entries) and modified simple fields', function()
{
    
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    setProtectedProperty($test, 'id', 7);
    
    $test->setValue('parent_int',111);
    $test->setValue('parent_string','AAA');
    $test->setValue('parent_sarray',[10,11,12]);
    
    $test->setValue('parent_int',919);
    $test->setValue('parent_string','AZA');
    
    $test->setValue('_classname','Dummy');
    $test->setValue('_uuid','11b47be8-05f1-4f7b-8a97-e1e6488dbd44');
    $test->setValue('_read_cap', null);
    $test->setValue('_write_cap', null);
    $test->setValue('_modify_cap', null);
    $test->setValue('_delete_cap', null);
    $test->setValue('_created_at', '2024-11-14 20:00:00');
    $test->setValue('_updated_at', '2024-11-14 20:00:00');
    $test->setValue('_tags',[]);
    $test->setValue('_attributes',[]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>7,'parent_int'=>919,'parent_string'=>'AZA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>0,'element'=>10]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>1,'element'=>11]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>2,'element'=>12]);
});

