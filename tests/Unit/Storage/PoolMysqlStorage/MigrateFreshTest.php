<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\PoolMysqlStorage\PoolMysqlStorage;
use Illuminate\Support\Facades\Schema;

uses(SunhillDatabaseTestCase::class);

require_once('PrepareStorage.php');

test('Migrate fresh for dummy', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'dummy'));
    Schema::drop('dummies');
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableHasColumn('dummies','dummyint');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
});

test('Migrate fresh for parentobject', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'parentobject'));
    Schema::drop('parentobjects');
    
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_int', 'integer');
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_string', 'varchar');
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','element','integer');
});

test('Migrate fresh for childobject', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    Schema::drop('parentobjects');
    Schema::drop('childobjects');
    
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_int', 'integer');
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_string', 'varchar');
    $this->assertDatabaseTableColumnIsType('childobjects', 'child_int', 'integer');
    $this->assertDatabaseTableColumnIsType('childobjects', 'child_string', 'varchar');
    
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','element','integer');

    $this->assertDatabaseHasTable('childobjects_child_sarray');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('childobjects_child_sarray','element','integer');
    
});