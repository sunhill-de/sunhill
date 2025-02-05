<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;

uses(SunhillDatabaseTestCase::class);

test('Append a skippingdummychild from database', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(SkippingDummyChild::getExpectedStructure());
    SkippingDummyChild::prepareDatabase($this);

    $test->setValue('_uuid','ABCD');
    $test->setValue('_read_cap',null);
    $test->setValue('_modify_cap',null);
    $test->setValue('_delete_cap',null);
    $test->setValue('_created_at','2025-02-05 17:54:10');
    $test->setValue('_modified_at','2025-02-05 17:54:10');
    $test->setValue('dummyint',1999);
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>$test->getID(),'dummyint'=>1999]);
    $this->assertDatabaseHas('skippingdummychildren',['id'=>$test->getID()]);
});

