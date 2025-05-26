<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\ChildObject;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Append a childobject with both arrays', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    prepareObjectDataset($test, ChildObject::class);
    
    $test->setValue('parent_int',1234);
    $test->setValue('parent_string','AIA');
    $test->setValue('parent_sarray',[11,22,33]);
    $test->setValue('child_int',4321);
    $test->setValue('child_string','AJA');
    $test->setValue('child_sarray',[111,222,333]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>$test->getID(),'parent_int'=>1234,'parent_string'=>'AIA']);
    $this->assertDatabaseHas('childobjects',['id'=>$test->getID(),'child_int'=>4321,'child_string'=>'AJA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>$test->getID(),'element'=>22]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>$test->getID(),'element'=>333]);
})->group('create');

test('Append a childobject with parent array', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    prepareObjectDataset($test, ChildObject::class);
    
    $test->setValue('parent_int',1234);
    $test->setValue('parent_string','AIA');
    $test->setValue('parent_sarray',[11,22,33]);
    $test->setValue('child_int',4321);
    $test->setValue('child_string','AJA');
    $test->setValue('child_sarray',[]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>$test->getID(),'parent_int'=>1234,'parent_string'=>'AIA']);
    $this->assertDatabaseHas('childobjects',['id'=>$test->getID(),'child_int'=>4321,'child_string'=>'AJA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>$test->getID(),'element'=>22]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>$test->getID()]);
})->group('create');

test('Append a childobject with child array', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    prepareObjectDataset($test, ChildObject::class);
    
    $test->setValue('parent_int',1234);
    $test->setValue('parent_string','AIA');
    $test->setValue('parent_sarray',[]);
    $test->setValue('child_int',4321);
    $test->setValue('child_string','AJA');
    $test->setValue('child_sarray',[111,222,333]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>$test->getID(),'parent_int'=>1234,'parent_string'=>'AIA']);
    $this->assertDatabaseHas('childobjects',['id'=>$test->getID(),'child_int'=>4321,'child_string'=>'AJA']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>$test->getID()]);
    $this->assertDatabaseHas('childobjects_child_sarray',['container_id'=>$test->getID()]);
})->group('create');

test('Append a childobject with both arrays empty', function()
{
    $test = prepareStorage(ChildObject::class, $this);
    prepareObjectDataset($test, ChildObject::class);
    
    $test->setValue('parent_int',1234);
    $test->setValue('parent_string','AIA');
    $test->setValue('parent_sarray',[]);
    $test->setValue('child_int',4321);
    $test->setValue('child_string','AJA');
    $test->setValue('child_sarray',[]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>$test->getID(),'parent_int'=>1234,'parent_string'=>'AIA']);
    $this->assertDatabaseHas('childobjects',['id'=>$test->getID(),'child_int'=>4321,'child_string'=>'AJA']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>$test->getID()]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>$test->getID()]);
})->group('create');
