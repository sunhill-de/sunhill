<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

uses(SunhillDatabaseTestCase::class);

test('Migrate with nothing to to', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ArrayOnlyChildObject::getExpectedStructure());
    ArrayOnlyChildObject::prepareDatabase($this);
    
    $test->migrate();
    $this->assertDatabaseHasTable('arrayonlychildobjects');
    $this->assertDatabaseTableHasColumn('arrayonlychildobjects','id');
    $this->assertDatabaseTableColumnIsType('arrayonlychildobjects', 'id', 'integer');    
    $this->assertDatabaseHasTable('arrayonlychildobjects_child_sarray');
    $this->assertDatabaseTableHasColumn('arrayonlychildobjects_child_sarray','container_id');
    $this->assertDatabaseTableColumnIsType('arrayonlychildobjects_child_sarray','container_id','integer');
    $this->assertDatabaseTableHasColumn('arrayonlychildobjects_child_sarray','index');
    $this->assertDatabaseTableColumnIsType('arrayonlychildobjects_child_sarray','index','integer');
    $this->assertDatabaseTableHasColumn('arrayonlychildobjects_child_sarray','element');
    $this->assertDatabaseTableColumnIsType('arrayonlychildobjects_child_sarray','element','integer');
});


test('Migrate fresh', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ArrayOnlyChildObject::getExpectedStructure());
    ArrayOnlyChildObject::prepareDatabase($this);
    Schema::drop('arrayonlychildobjects');
    Schema::drop('arrayonlychildobjects_child_sarray');
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('arrayonlychildobjects');
    $this->assertDatabaseTableHasColumn('arrayonlychildobjects','id');
    $this->assertDatabaseTableColumnIsType('arrayonlychildobjects', 'id', 'integer');
    $this->assertDatabaseHasTable('arrayonlychildobjects_child_sarray');
    $this->assertDatabaseTableHasColumn('arrayonlychildobjects_child_sarray','container_id');
    $this->assertDatabaseTableColumnIsType('arrayonlychildobjects_child_sarray','container_id','integer');
    $this->assertDatabaseTableHasColumn('arrayonlychildobjects_child_sarray','index');
    $this->assertDatabaseTableColumnIsType('arrayonlychildobjects_child_sarray','index','integer');
    $this->assertDatabaseTableHasColumn('arrayonlychildobjects_child_sarray','element');
    $this->assertDatabaseTableColumnIsType('arrayonlychildobjects_child_sarray','element','integer');
});

