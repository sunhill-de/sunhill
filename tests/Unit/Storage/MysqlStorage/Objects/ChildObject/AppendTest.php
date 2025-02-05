<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\ChildObject;

uses(SunhillDatabaseTestCase::class);

test('Append a childobject with both arrays', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);

    $test->setValue('_uuid','ABCD');
    $test->setValue('_read_cap',null);
    $test->setValue('_modify_cap',null);
    $test->setValue('_delete_cap',null);
    $test->setValue('_created_at','2025-02-05 17:54:10');
    $test->setValue('_modified_at','2025-02-05 17:54:10');
    
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
});

test('Append a childobject with parent array', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    $test->setValue('_uuid','ABCD');
    $test->setValue('_read_cap',null);
    $test->setValue('_modify_cap',null);
    $test->setValue('_delete_cap',null);
    $test->setValue('_created_at','2025-02-05 17:54:10');
    $test->setValue('_modified_at','2025-02-05 17:54:10');
    
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
});

test('Append a childobject with child array', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    $test->setValue('_uuid','ABCD');
    $test->setValue('_read_cap',null);
    $test->setValue('_modify_cap',null);
    $test->setValue('_delete_cap',null);
    $test->setValue('_created_at','2025-02-05 17:54:10');
    $test->setValue('_modified_at','2025-02-05 17:54:10');
    
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
});

test('Append a childobject with both arrays empty', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    $test->setValue('_uuid','ABCD');
    $test->setValue('_read_cap',null);
    $test->setValue('_modify_cap',null);
    $test->setValue('_delete_cap',null);
    $test->setValue('_created_at','2025-02-05 17:54:10');
    $test->setValue('_modified_at','2025-02-05 17:54:10');
    
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
});

