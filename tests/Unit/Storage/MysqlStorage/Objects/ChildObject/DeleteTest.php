<?php

use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Tests\SunhillDatabaseTestCase;

uses(SunhillDatabaseTestCase::class);

test('Delete a childobject with both arrays', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    $test->delete(9);
    
    $this->assertDatabaseMissing('parentobjects',['id'=>9]);
    $this->assertDatabaseMissing('childobjects',['id'=>9]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>9]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>9]);
});

test('Delete a childobject with parent array', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    $test->delete(10);
    
    $this->assertDatabaseMissing('parentobjects',['id'=>10]);
    $this->assertDatabaseMissing('childobjects',['id'=>10]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>10]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>10]);
});

test('Read a childobject with child array', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    $test->delete(11);
    
    $this->assertDatabaseMissing('parentobjects',['id'=>11]);
    $this->assertDatabaseMissing('childobjects',['id'=>11]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>11]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>11]);
});

test('Read a childobject with both arrays empty', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    $test->delete(12);
    
    $this->assertDatabaseMissing('parentobjects',['id'=>12]);
    $this->assertDatabaseMissing('childobjects',['id'=>12]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>12]);
    $this->assertDatabaseMissing('childobjects_child_sarray',['container_id'=>12]);
});
