<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');


test('Append a arrayonlychildobject with parent und child array', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    prepareObjectDataset($test, ArrayOnlyChildObject::class);
        
    $test->setValue('parent_int',5445);
    $test->setValue('parent_string','AXA');
    $test->setValue('parent_sarray',[111,222,333]);
    $test->setValue('child_sarray',[121,232,343]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>$test->getID(),'parent_int'=>5445]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>$test->getID()]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>$test->getID(),'element'=>111]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>$test->getID(),'element'=>232]);
})->group('create');

test('Append a arrayonlychildobject with parent array only', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    prepareObjectDataset($test, ArrayOnlyChildObject::class);
    
    $test->setValue('parent_int',5445);
    $test->setValue('parent_string','AXA');
    $test->setValue('parent_sarray',[111,222,333]);
    $test->setValue('child_sarray',[]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>$test->getID(),'parent_int'=>5445]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>$test->getID()]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>$test->getID(),'element'=>111]);
    $this->assertDatabaseMissing('arrayonlychildobjects_child_sarray',['container_id'=>$test->getID()]);
})->group('create');

test('Append a arrayonlychildobject with child array only', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    prepareObjectDataset($test, ArrayOnlyChildObject::class);
    
    $test->setValue('parent_int',5445);
    $test->setValue('parent_string','AXA');
    $test->setValue('parent_sarray',[]);
    $test->setValue('child_sarray',[121,232,343]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>$test->getID(),'parent_int'=>5445]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>$test->getID()]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>$test->getID()]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>$test->getID(),'element'=>232]);
})->group('create');

test('Append a arrayonlychildobject with no array', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    prepareObjectDataset($test, ArrayOnlyChildObject::class);
    
    $test->setValue('parent_int',5445);
    $test->setValue('parent_string','AXA');
    $test->setValue('parent_sarray',[]);
    $test->setValue('child_sarray',[]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>$test->getID(),'parent_int'=>5445]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>$test->getID()]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>$test->getID()]);
    $this->assertDatabaseMissing('arrayonlychildobjects_child_sarray',['container_id'=>$test->getID()]);
})->group('create');

