<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;

uses(SunhillDatabaseTestCase::class);

test('Append a arrayonlychildobject with child arrays', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ArrayOnlyChildObject::getExpectedStructure());
    ArrayOnlyChildObject::prepareDatabase($this);
    
    $test->setValue('_uuid','ABCD');
    $test->setValue('_read_cap',null);
    $test->setValue('_modify_cap',null);
    $test->setValue('_delete_cap',null);
    $test->setValue('_created_at','2025-02-05 17:54:10');
    $test->setValue('_modified_at','2025-02-05 17:54:10');
    
    $test->setValue('parent_int',5445);
    $test->setValue('parent_string','AXA');
    $test->setValue('parent_sarray',[111,222,333]);
    $test->setValue('child_sarray',[121,232,343]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>$test->getID(),'parent_int'=>5445]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>$test->getID()]);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>$test->getID(),'element'=>111]);
    $this->assertDatabaseHas('arrayonlychildobjects_child_sarray',['container_id'=>$test->getID(),'element'=>232]);
});

test('Append a childobject with no array', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ArrayOnlyChildObject::getExpectedStructure());
    ArrayOnlyChildObject::prepareDatabase($this);
    
    $test->setValue('_uuid','ABCD');
    $test->setValue('_read_cap',null);
    $test->setValue('_modify_cap',null);
    $test->setValue('_delete_cap',null);
    $test->setValue('_created_at','2025-02-05 17:54:10');
    $test->setValue('_modified_at','2025-02-05 17:54:10');
    
    $test->setValue('parent_int',5445);
    $test->setValue('parent_string','AXA');
    $test->setValue('parent_sarray',[]);
    $test->setValue('child_sarray',[]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>$test->getID(),'parent_int'=>5445]);
    $this->assertDatabaseHas('arrayonlychildobjects',['id'=>$test->getID()]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>$test->getID()]);
    $this->assertDatabaseMissing('arrayonlychildobjects_child_sarray',['container_id'=>$test->getID()]);
});

