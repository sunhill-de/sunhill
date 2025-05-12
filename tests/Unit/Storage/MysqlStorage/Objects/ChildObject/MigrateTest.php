<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Illuminate\Support\Facades\Schema;

uses(SunhillDatabaseTestCase::class);

test('Migrate for childobject with nothing to do', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_int', 'integer');
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_string', 'string');
    $this->assertDatabaseTableColumnIsType('childobjects', 'child_int', 'integer');
    $this->assertDatabaseTableColumnIsType('childobjects', 'child_string', 'string');
    
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','element','integer');
    
    $this->assertDatabaseHasTable('childobjects_child_sarray');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','element','integer');
    
});

test('Migrate fresh for childobject (both not existant)', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    Schema::drop('parentobjects');
    Schema::drop('parentobjects_parent_sarray');
    Schema::drop('childobjects');
    Schema::drop('childobjects_child_sarray');
    
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_int', 'integer');
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_string', 'string');
    $this->assertDatabaseTableColumnIsType('childobjects', 'child_int', 'integer');
    $this->assertDatabaseTableColumnIsType('childobjects', 'child_string', 'string');
    
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','element','integer');
    
    $this->assertDatabaseHasTable('childobjects_child_sarray');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','element','integer');    
});

test('Migrate fresh for childobject (child not existant)', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    Schema::drop('childobjects');
    Schema::drop('childobjects_child_sarray');
    
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_int', 'integer');
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_string', 'string');
    $this->assertDatabaseTableColumnIsType('childobjects', 'child_int', 'integer');
    $this->assertDatabaseTableColumnIsType('childobjects', 'child_string', 'string');
    
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','element','integer');
    
    $this->assertDatabaseHasTable('childobjects_child_sarray');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','element','integer');    
});

test('Migrate fresh for childobject (parent not existant)', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    Schema::drop('parentobjects');
    Schema::drop('parentobjects_parent_sarray');
    
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_int', 'integer');
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_string', 'string');
    $this->assertDatabaseTableColumnIsType('childobjects', 'child_int', 'integer');
    $this->assertDatabaseTableColumnIsType('childobjects', 'child_string', 'string');
    
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','element','integer');
    
    $this->assertDatabaseHasTable('childobjects_child_sarray');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','element','integer');   
});

test('Migrate fresh for childobject (child array not existant)', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    Schema::drop('childobjects_child_sarray');
    
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_int', 'integer');
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_string', 'string');
    $this->assertDatabaseTableColumnIsType('childobjects', 'child_int', 'integer');
    $this->assertDatabaseTableColumnIsType('childobjects', 'child_string', 'string');
    
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','element','integer');
    
    $this->assertDatabaseHasTable('childobjects_child_sarray');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','element','integer');
});

test('Migrate fresh for childobject (parent array not existant)', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    Schema::drop('parentobjects_parent_sarray');
    
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_int', 'integer');
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_string', 'string');
    $this->assertDatabaseTableColumnIsType('childobjects', 'child_int', 'integer');
    $this->assertDatabaseTableColumnIsType('childobjects', 'child_string', 'string');
    
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','element','integer');
    
    $this->assertDatabaseHasTable('childobjects_child_sarray');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','element','integer');
});

test('Migrate fresh for childobject (both arrays not existant)', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    Schema::drop('parentobjects_parent_sarray');
    Schema::drop('childobjects_child_sarray');
    
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_int', 'integer');
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_string', 'string');
    $this->assertDatabaseTableColumnIsType('childobjects', 'child_int', 'integer');
    $this->assertDatabaseTableColumnIsType('childobjects', 'child_string', 'string');
    
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','element','integer');
    
    $this->assertDatabaseHasTable('childobjects_child_sarray');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','element','integer');
});

