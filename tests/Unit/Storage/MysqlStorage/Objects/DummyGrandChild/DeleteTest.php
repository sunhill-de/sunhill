<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;

uses(SunhillDatabaseTestCase::class);

test('Delete an DummyChild', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(DummyGrandChild::getExpectedStructure());
    DummyGrandChild::prepareDatabase($this);
    
    $test->delete(15);
    
    $this->assertDatabaseMissing('dummies',['id'=>15]);
    $this->assertDatabaseMissing('dummychildren',['container_id'=>15]);
    $this->assertDatabaseMissing('dummygrandchildren',['container_id'=>15]);
})->group('delete');

