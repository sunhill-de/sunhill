<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;
use Sunhill\Tests\TestSupport\Objects\DummyChild;

uses(SunhillDatabaseTestCase::class);

test('Delete an DummyChild', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(DummyChild::getExpectedStructure());
    DummyChild::prepareDatabase($this);
    
    $test->delete(13);
    
    $this->assertDatabaseMissing('dummies',['id'=>13]);
    $this->assertDatabaseMissing('dummychildren',['container_id'=>13]);
})->group('delete');

