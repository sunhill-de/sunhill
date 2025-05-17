<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Append a skippingdummygrandchild from database', function()
{
    $test = prepareStorage(SkippingDummyGrandChild::class, $this);
    prepareObjectDataset($test, SkippingDummyGrandChild::class);
    
    $test->setValue('dummyint',1999);
    $test->setValue('dummygrandchildint',1997);
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>$test->getID(),'dummyint'=>1999]);
    $this->assertDatabaseHas('skippingdummychildren',['id'=>$test->getID()]);
    $this->assertDatabaseHas('skippingdummygrandchildren',['id'=>$test->getID(),'dummygrandchildint'=>1997]);
})->group('append');

