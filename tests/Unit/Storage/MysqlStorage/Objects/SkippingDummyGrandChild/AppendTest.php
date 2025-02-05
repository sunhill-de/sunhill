<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;

uses(SunhillDatabaseTestCase::class);

test('Append a skippingdummygrandchild from database', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(SkippingDummyGrandChild::getExpectedStructure());
    SkippingDummyGrandChild::prepareDatabase($this);
    $test->setValue('_uuid','ABCD');
    $test->setValue('_read_cap',null);
    $test->setValue('_modify_cap',null);
    $test->setValue('_delete_cap',null);
    $test->setValue('_created_at','2025-02-05 17:54:10');
    $test->setValue('_modified_at','2025-02-05 17:54:10');
    
    $test->setValue('dummyint',1999);
    $test->setValue('dummygrandchildint',1997);
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>$test->getID(),'dummyint'=>1999]);
    $this->assertDatabaseHas('skippingdummychildren',['id'=>$test->getID()]);
    $this->assertDatabaseHas('skippingdummygrandchildren',['id'=>$test->getID(),'dummygrandchildint'=>1997]);
});

