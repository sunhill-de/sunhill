<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\ParentReference;

uses(SunhillDatabaseTestCase::class);

test('Append a parentreference with reference and array', function()
{    
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentReference::getExpectedStructure());
    ParentReference::prepareDatabase($this);
    $test->setValue('_uuid','ABCD');
    $test->setValue('_read_cap',null);
    $test->setValue('_modify_cap',null);
    $test->setValue('_delete_cap',null);
    $test->setValue('_created_at','2025-02-05 17:54:10');
    $test->setValue('_modified_at','2025-02-05 17:54:10');
    
    $test->setValue('parent_int',1213);
    $test->setValue('parent_reference',1);
    $test->setValue('parent_rarray',[1,2,3]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentreferences',['id'=>$test->getID(),'parent_int'=>1213,'parent_reference'=>1]);
    $this->assertDatabaseHas('parentreferences_parent_rarray',['container_id'=>$test->getID(),'element'=>3]);
});

test('Append a parentreference only with array', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentReference::getExpectedStructure());
    ParentReference::prepareDatabase($this);
    $test->setValue('_uuid','ABCD');
    $test->setValue('_read_cap',null);
    $test->setValue('_modify_cap',null);
    $test->setValue('_delete_cap',null);
    $test->setValue('_created_at','2025-02-05 17:54:10');
    $test->setValue('_modified_at','2025-02-05 17:54:10');
    
    $test->setValue('parent_int',1213);
    $test->setValue('parent_reference',null);
    $test->setValue('parent_rarray',[1,2,3]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentreferences',['id'=>$test->getID(),'parent_int'=>1213,'parent_reference'=>null]);
    $this->assertDatabaseHas('parentreferences_parent_rarray',['container_id'=>$test->getID(),'element'=>3]);
});

test('Append a parentreference with no references', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentReference::getExpectedStructure());
    ParentReference::prepareDatabase($this);
    $test->setValue('_uuid','ABCD');
    $test->setValue('_read_cap',null);
    $test->setValue('_modify_cap',null);
    $test->setValue('_delete_cap',null);
    $test->setValue('_created_at','2025-02-05 17:54:10');
    $test->setValue('_modified_at','2025-02-05 17:54:10');
    
    $test->setValue('parent_int',1213);
    $test->setValue('parent_reference',null);
    $test->setValue('parent_rarray',[]);
    
    $test->commit();
    
    $this->assertDatabaseHas('parentreferences',['id'=>$test->getID(),'parent_int'=>1213,'parent_reference'=>null]);
    $this->assertDatabaseMissing('parentreferences_parent_rarray',['container_id'=>$test->getID()]);
});
