<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Migrate with nothing to to', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    
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
})->group('migrate');


test('Migrate fresh', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
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
})->group('migrate');

test('Migrate drop field in child', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    Schema::drop('arrayonlychildobjects');
    Schema::create('arrayonlychildobjects', function($table)
    {
       $table->integer('id');
       $table->string('dropfield');
    });
    $test->migrate();
    
    $this->assertDatabaseHasTable('arrayonlychildobjects');
    $this->assertDatabaseTableHasColumn('arrayonlychildobjects','id');
    $this->assertDatabaseTableColumnIsType('arrayonlychildobjects', 'id', 'integer');
    $this->assertDatabaseTableHasNotColumn('arrayonlychildobject','dropfield');
    
    $this->assertDatabaseTableColumnIsType('arrayonlychildobjects_child_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('arrayonlychildobjects_child_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('arrayonlychildobjects_child_sarray','element','integer');
})->group('migrate');

test('Migrate drop array field', function()
{
    $test = prepareStorage(ArrayOnlyChildObject::class, $this);
    Schema::create('arrayonlychildobjects_droparray', function($table)
    {
        $table->integer('container_id');
        $table->integer('index');
        $table->string('element');
    });
    $test->migrate();
    
    $this->assertDatabaseHasTable('arrayonlychildobjects');
    $this->assertDatabaseTableHasColumn('arrayonlychildobjects','id');
    $this->assertDatabaseTableColumnIsType('arrayonlychildobjects', 'id', 'integer');
    $this->assertDatabaseTableHasNotColumn('arrayonlychildobject','dropfield');
    
    $this->assertDatabaseTableColumnIsType('arrayonlychildobjects_child_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('arrayonlychildobjects_child_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('arrayonlychildobjects_child_sarray','element','integer');
    
    $this->assertDatabaseHasNotTable('arrayonlychildobject_droparray');
});