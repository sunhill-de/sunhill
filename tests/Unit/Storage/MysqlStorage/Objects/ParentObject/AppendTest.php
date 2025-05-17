<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Append a parentobject with array', function()
{    
    $test = prepareStorage(ParentObject::class, $this);
    prepareObjectDataset($test, ParentObject::class);
    
    $test->setValue('parent_int',1509);
    $test->setValue('parent_string','APA');
    $test->setValue('parent_sarray',[11,22,33]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>$test->getID(),'parent_int'=>1509,'parent_string'=>'APA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>$test->getID(),'index'=>0,'element'=>11]);
})->group('append');

test('Append a parentobject with empty array', function()
{
    $test = prepareStorage(ParentObject::class, $this);
    prepareObjectDataset($test, ParentObject::class);
    
    $test->setValue('parent_int',1509);
    $test->setValue('parent_string','APA');
    $test->setValue('parent_sarray',[]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>$test->getID(),'parent_int'=>1509,'parent_string'=>'APA']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>$test->getID()]);
    
})->group('append');
