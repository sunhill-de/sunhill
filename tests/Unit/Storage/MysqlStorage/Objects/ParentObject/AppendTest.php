<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SunhillDatabaseTestCase::class);

test('Append a parentobject with array', function()
{    
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    $test->setValue('_uuid','ABCD');
    $test->setValue('_read_cap',null);
    $test->setValue('_modify_cap',null);
    $test->setValue('_delete_cap',null);
    $test->setValue('_created_at','2025-02-05 17:54:10');
    $test->setValue('_modified_at','2025-02-05 17:54:10');
    
    $test->setValue('parent_int',1509);
    $test->setValue('parent_string','APA');
    $test->setValue('parent_sarray',[11,22,33]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>$test->getID(),'parent_int'=>1509,'parent_string'=>'APA']);
    $this->assertDatabaseHas('parentobjects_parent_sarray',['container_id'=>$test->getID(),'index'=>0,'element'=>11]);
});

test('Append a parentobject with empty array', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    $test->setValue('_uuid','ABCD');
    $test->setValue('_read_cap',null);
    $test->setValue('_modify_cap',null);
    $test->setValue('_delete_cap',null);
    $test->setValue('_created_at','2025-02-05 17:54:10');
    $test->setValue('_modified_at','2025-02-05 17:54:10');
    
    $test->setValue('parent_int',1509);
    $test->setValue('parent_string','APA');
    $test->setValue('parent_sarray',[]);
    $test->commit();
    
    $this->assertDatabaseHas('parentobjects',['id'=>$test->getID(),'parent_int'=>1509,'parent_string'=>'APA']);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>$test->getID()]);
    
});
