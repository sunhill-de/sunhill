<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;

uses(SunhillDatabaseTestCase::class);

test('Delete an ArrayOnlyChildObject with array entries', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ArrayOnlyChildObject::getExpectedStructure());
    ArrayOnlyChildObject::prepareDatabase($this);
    
    $test->delete(20);
    
    $this->assertDatabaseMissing('arrayonlychildobjects',['id'=>20]);
    $this->assertDatabaseMissing('arrayonlychildobjects_child_sarray',['container_id'=>20]);
    $this->assertDatabaseMissing('parentobjects',['id'=>20]);
})->group('delete');

test('Delete an ArrayOnlyChildObject without array entries', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ArrayOnlyChildObject::getExpectedStructure());
    ArrayOnlyChildObject::prepareDatabase($this);
    
    $test->delete(21);
    
    $this->assertDatabaseMissing('arrayonlychildobjects',['id'=>21]);
    $this->assertDatabaseMissing('arrayonlychildobjects_child_sarray',['container_id'=>21]);
    $this->assertDatabaseMissing('parentobjects',['id'=>21]);
})->group('delete');