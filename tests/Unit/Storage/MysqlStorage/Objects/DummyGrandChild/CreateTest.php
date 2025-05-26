<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Append a dummygrandchild from database', function()
{
    $test = prepareStorage(DummygrandChild::class, $this);
    prepareObjectDataset($test, DummyGrandChild::class);
    
    $test->setValue('dummyint',1999);
    $test->setValue('dummychildint',1998);
    $test->setValue('dummygrandchildint',1997);
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>$test->getID(),'dummyint'=>1999]);
    $this->assertDatabaseHas('dummychildren',['id'=>$test->getID(),'dummychildint'=>1998]);    
    $this->assertDatabaseHas('dummygrandchildren',['id'=>$test->getID(),'dummygrandchildint'=>1997]);
})->group('create');

