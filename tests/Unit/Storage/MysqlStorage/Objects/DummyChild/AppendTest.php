<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\DummyChild;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Append a dummychild to database', function()
{
    $test = prepareStorage(DummyChild::class, $this);
    prepareObjectDataset($test, DummyChild::class);
    
    $test->setValue('dummyint',1999);
    $test->setValue('dummychildint',1998);
    $test->commit();
    
    $this->assertDatabaseHas('dummies',['id'=>$test->getID(),'dummyint'=>1999]);
    $this->assertDatabaseHas('dummychildren',['id'=>$test->getID(),'dummychildint'=>1998]);
})->group('append');

