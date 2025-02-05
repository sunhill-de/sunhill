<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;
use Illuminate\Support\Facades\DB;

uses(SunhillDatabaseTestCase::class);

test('Append a Dummy', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(Dummy::getExpectedStructure());
    Dummy::prepareDatabase($this);
    $test->setValue('_uuid','ABCD');
    $test->setValue('_read_cap',null);
    $test->setValue('_modify_cap',null);
    $test->setValue('_delete_cap',null);
    $test->setValue('_created_at','2025-02-05 17:54:10');
    $test->setValue('_modified_at','2025-02-05 17:54:10');
    $test->setValue('dummyint',1999);
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['dummyint'=>1999]);
    $this->assertDatabaseHas('objects', ['_classname'=>'Dummy','_uuid'=>'ABCD','_created_at'=>'2025-02-05 17:54:10']);
});

test('Append a Dummy with tags', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(Dummy::getExpectedStructure());
    Dummy::prepareDatabase($this);
    $test->setValue('_tags',[1,2,3]);
    $test->setValue('_uuid','ABCD');
    $test->setValue('_read_cap',null);
    $test->setValue('_modify_cap',null);
    $test->setValue('_delete_cap',null);
    $test->setValue('_created_at','2025-02-05 17:54:10');
    $test->setValue('_modified_at','2025-02-05 17:54:10');
    $test->setValue('dummyint',1999);
    $test->commit();
    
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>$test->getID(),'tag_id'=>1]);
    $this->assertDatabaseHas('tagobjectassigns',['container_id'=>$test->getID(),'tag_id'=>2]);
});
