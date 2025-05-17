<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Test update with nothing to do (array exists)', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    pretendLoaded($test, 20, ArrayOnlyChildObject::class);
    prepareObjectDataset($test, 'ArrayOnlyChildObject');

    $test->commit();
    $this->assertDatabaseHas('parentobjects',['id'=>20,'parent_int'=>5555,'parent_string'=>'ERE']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>1,'element'=>41]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>2,'element'=>42]);    
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>20]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>0,'element'=>2000]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>1,'element'=>2100]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>2,'element'=>2200]);
})->group('update');

test('Test update with nothing to do (array doesnt exists)', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    pretendLoaded($test, 21, ArrayOnlyChildObject::class);
    prepareObjectDataset($test, 'ArrayOnlyChildObject');
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>21,'parent_int'=>6666,'parent_string'=>'FRF']);
    $this->assertDatabaseMissing('parentobjects',['container_id'=>21]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>21]);    
    $this->assertDatabaseMissing('arrayonlychildobjects_child_sarray',['container_id'=>21]);
})->group('update');

test('Test update update simple field with filled arrays', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    pretendLoaded($test, 20, ArrayOnlyChildObject::class);
    prepareObjectDataset($test, 'ArrayOnlyChildObject');
    $test->setValue('parent_int',5432);
    
    $test->commit();
    $this->assertDatabaseHas('parentobjects',['id'=>20,'parent_int'=>5432,'parent_string'=>'ERE']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>1,'element'=>41]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>2,'element'=>42]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>20]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>0,'element'=>2000]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>1,'element'=>2100]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>2,'element'=>2200]);
})->group('update');

test('Test update simple field with empty arrays', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    pretendLoaded($test, 21, ArrayOnlyChildObject::class);
    prepareObjectDataset($test, 'ArrayOnlyChildObject');
    $test->setValue('parent_int',5432);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>21,'parent_int'=>5432,'parent_string'=>'FRF']);
    $this->assertDatabaseMissing('parentobjects',['container_id'=>21]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>21]);
    $this->assertDatabaseMissing('arrayonlychildobjects_child_sarray',['container_id'=>21]);
})->group('update');

test('Test update append element to existing parent array', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    pretendLoaded($test, 20, ArrayOnlyChildObject::class);
    prepareObjectDataset($test, 'ArrayOnlyChildObject');
    $test->setValue('parent_sarray',[40,41,42,43]);
    
    $test->commit();
    $this->assertDatabaseHas('parentobjects',['id'=>20,'parent_int'=>5555,'parent_string'=>'ERE']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>1,'element'=>41]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>2,'element'=>42]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>3,'element'=>43]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>20]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>0,'element'=>2000]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>1,'element'=>2100]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>2,'element'=>2200]);
})->group('update');

test('Test update change element of existing parent array', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    pretendLoaded($test, 20, ArrayOnlyChildObject::class);
    prepareObjectDataset($test, 'ArrayOnlyChildObject');
    $test->setValue('parent_sarray',[40,66,42]);
    
    $test->commit();
    $this->assertDatabaseHas('parentobjects',['id'=>20,'parent_int'=>5555,'parent_string'=>'ERE']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>1,'element'=>66]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>2,'element'=>42]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>20]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>0,'element'=>2000]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>1,'element'=>2100]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>2,'element'=>2200]);
})->group('update');

test('Test update delete element from existing parent array', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    pretendLoaded($test, 20, ArrayOnlyChildObject::class);
    prepareObjectDataset($test, 'ArrayOnlyChildObject');
    $test->setValue('parent_sarray',[40,41]);
    
    $test->commit();
    $this->assertDatabaseHas('parentobjects',['id'=>20,'parent_int'=>5555,'parent_string'=>'ERE']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>1,'element'=>41]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>20,'index'=>2,'element'=>42]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>20]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>0,'element'=>2000]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>1,'element'=>2100]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>2,'element'=>2200]);
})->group('update');

test('Test update clear existing parent array', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    pretendLoaded($test, 20, ArrayOnlyChildObject::class);
    prepareObjectDataset($test, 'ArrayOnlyChildObject');
    $test->setValue('parent_sarray',[]);
    
    $test->commit();
    $this->assertDatabaseHas('parentobjects',['id'=>20,'parent_int'=>5555,'parent_string'=>'ERE']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>20]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>20]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>0,'element'=>2000]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>1,'element'=>2100]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>2,'element'=>2200]);
})->group('update');

test('Test update previously empty parent array filled with elements', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    pretendLoaded($test, 21, ArrayOnlyChildObject::class);
    prepareObjectDataset($test, 'ArrayOnlyChildObject');
    $test->setValue('parent_sarray',[1,2,3]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>21,'parent_int'=>6666,'parent_string'=>'FRF']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>21,'index'=>0,'element'=>1]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>21,'index'=>1,'element'=>2]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>21,'index'=>2,'element'=>3]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>21]);
    $this->assertDatabaseMissing('arrayonlychildobjects_child_sarray',['container_id'=>21]);
})->group('update');

test('Test update previously empty child array filled with elements', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    pretendLoaded($test, 21, ArrayOnlyChildObject::class);
    prepareObjectDataset($test, 'ArrayOnlyChildObject');
    $test->setValue('child_sarray',[1,2,3]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>21,'parent_int'=>6666,'parent_string'=>'FRF']);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>21]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>21,'index'=>0,'element'=>1]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>21,'index'=>1,'element'=>2]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>21,'index'=>2,'element'=>3]);
})->group('update');

test('Test update child array (append element)', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    pretendLoaded($test, 20, ArrayOnlyChildObject::class);
    prepareObjectDataset($test, 'ArrayOnlyChildObject');
    $test->setValue('child_sarray',[2000,2100,2200,2300]);
    
    $test->commit();
    $this->assertDatabaseHas('parentobjects',['id'=>20,'parent_int'=>5555,'parent_string'=>'ERE']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>1,'element'=>41]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>2,'element'=>42]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>20]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>0,'element'=>2000]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>1,'element'=>2100]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>2,'element'=>2200]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>3,'element'=>2300]);
})->group('update');

test('Test update child array (delete element)', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    pretendLoaded($test, 20, ArrayOnlyChildObject::class);
    prepareObjectDataset($test, 'ArrayOnlyChildObject');
    $test->setValue('child_sarray',[2000,2100]);
    
    $test->commit();
    $this->assertDatabaseHas('parentobjects',['id'=>20,'parent_int'=>5555,'parent_string'=>'ERE']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>1,'element'=>41]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>2,'element'=>42]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>20]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>0,'element'=>2000]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>1,'element'=>2100]);
    $this->assertDatabaseMissing('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>2,'element'=>2200]);
})->group('update');

test('Test update child array (change element)', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    pretendLoaded($test, 20, ArrayOnlyChildObject::class);
    prepareObjectDataset($test, 'ArrayOnlyChildObject');
    $test->setValue('child_sarray',[2000,2222,2200]);
    
    $test->commit();
    $this->assertDatabaseHas('parentobjects',['id'=>20,'parent_int'=>5555,'parent_string'=>'ERE']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>1,'element'=>41]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>2,'element'=>42]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>20]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>0,'element'=>2000]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>1,'element'=>2222]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>20,'index'=>2,'element'=>2200]);
})->group('update');

test('Test update child array (clear array)', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    pretendLoaded($test, 20, ArrayOnlyChildObject::class);
    prepareObjectDataset($test, 'ArrayOnlyChildObject');
    $test->setValue('child_sarray',[]);
    
    $test->commit();
    $this->assertDatabaseHas('parentobjects',['id'=>20,'parent_int'=>5555,'parent_string'=>'ERE']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>0,'element'=>40]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>1,'element'=>41]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>20,'index'=>2,'element'=>42]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>20]);
    $this->assertDatabaseMissing('arrayonlychildobjects_child_sarray',['container_id'=>20]);
})->group('update');

