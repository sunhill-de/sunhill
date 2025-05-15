<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Update a parentobject with with nothing to do (filled array)', function()
{
    $test = prepareStorage(ParentObject::class, $this);
    pretendLoaded($test, 7, ParentObject::class);
    prepareObjectDataset($test, 'ParentObject');
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>7,'parent_int'=>111,'parent_string'=>'AAA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>0,'element'=>10]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>1,'element'=>11]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>2,'element'=>12]);
})->group('update');

test('Update a parentobject with with nothing to do (empty array)', function()
{
    $test = prepareStorage(ParentObject::class, $this);
    pretendLoaded($test, 8, ParentObject::class);
    prepareObjectDataset($test, 'ParentObject');
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>8,'parent_int'=>222,'parent_string'=>'BBB']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>8]);
})->group('update');

test('Update a parentobject with modified only simple fields', function()
{
    $test = prepareStorage(ParentObject::class, $this);
    pretendLoaded($test, 7, ParentObject::class);
    prepareObjectDataset($test, 'ParentObject');
    
    $test->setValue('parent_int',919);
    $test->setValue('parent_string','AZA');
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>7,'parent_int'=>919,'parent_string'=>'AZA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>0,'element'=>10]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>1,'element'=>11]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>2,'element'=>12]);
})->group('update');

test('Update a parentobject with modified parent_array (added element)', function()
{
    $test = prepareStorage(ParentObject::class, $this);
    pretendLoaded($test, 7, ParentObject::class);
    prepareObjectDataset($test, 'ParentObject');
    
    setProtectedProperty($test, 'id', 7);
    $test->setValue('parent_sarray',[10,11,12,13]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>7,'parent_int'=>111,'parent_string'=>'AAA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>0,'element'=>10]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>1,'element'=>11]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>2,'element'=>12]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>3,'element'=>13]);
})->group('update');

test('Update a parentobject with modified parent_array (deleted element)', function()
{
    $test = prepareStorage(ParentObject::class, $this);
    pretendLoaded($test, 7, ParentObject::class);
    prepareObjectDataset($test, 'ParentObject');
    
    setProtectedProperty($test, 'id', 7);
    $test->setValue('parent_sarray',[10,11]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>7,'parent_int'=>111,'parent_string'=>'AAA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>0,'element'=>10]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>1,'element'=>11]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>7,'index'=>2,'element'=>12]);
})->group('update');

test('Update a parentobject with modified parent_array (cleared)', function()
{
    $test = prepareStorage(ParentObject::class, $this);
    pretendLoaded($test, 7, ParentObject::class);
    prepareObjectDataset($test, 'ParentObject');
    
    setProtectedProperty($test, 'id', 7);
    $test->setValue('parent_sarray',[]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>7,'parent_int'=>111,'parent_string'=>'AAA']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>7]);
})->group('update');

test('Update a parentobject with modified array (all entries) and modified simple fields', function()
{
    $test = prepareStorage(ParentObject::class, $this);
    pretendLoaded($test, 7, ParentObject::class);
    prepareObjectDataset($test, 'ParentObject');
    
    $test->setValue('parent_int',919);
    $test->setValue('parent_string','AZA');
    $test->setValue('parent_sarray',[100,110,120]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>7,'parent_int'=>919,'parent_string'=>'AZA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>0,'element'=>100]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>1,'element'=>110]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>7,'index'=>2,'element'=>120]);
})->group('update');

test('Update a parentobject modify simple fields with empty array', function()
{
    $test = prepareStorage(ParentObject::class, $this);
    pretendLoaded($test, 8, ParentObject::class);
    $test->setValue('parent_string','XYZ');
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>8,'parent_int'=>222,'parent_string'=>'XYZ']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>8]);
})->group('update');

test('Update a parentobject elements in previously empty array', function()
{
    $test = prepareStorage(ParentObject::class, $this);
    pretendLoaded($test, 8, ParentObject::class);
    prepareObjectDataset($test, 'ParentObject');
    $test->setValue('parent_sarray',[12,13,14]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>8,'parent_int'=>222,'parent_string'=>'BBB']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>8,'index'=>0,'element'=>12]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>8,'index'=>1,'element'=>13]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>8,'index'=>2,'element'=>14]);
})->group('update');

