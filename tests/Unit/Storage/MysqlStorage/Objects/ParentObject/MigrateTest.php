<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Illuminate\Support\Facades\Schema;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;

uses(SunhillDatabaseTestCase::class);

test('Migrate fresh for parentobject', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    Schema::drop('parentobjects');
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('parentobjects');
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
    
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_int', 'integer');
    $this->assertDatabaseTableColumnIsType('parentobjects', 'parent_string', 'string');
    
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','index','integer');
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','element','integer');
});

test('column was dropped', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    Schema::dropIfExists('parentobjects');
    Schema::create('parentobjects', function($table)
    {
        $table->integer('id');
        $table->integer('parent_int');
        $table->string('parent_string');
        $table->integer('dropped');
        $table->primary('id');
    });
    Schema::dropIfExists('parentobjects_parent_sarray');
    Schema::create('parentobjects_parent_sarray', function($table)
    {
        $table->integer('container_id');
        $table->integer('index');
        $table->integer('element');
    });
    
    $test->migrate();
    
    $this->assertDatabaseTableHasColumn('parentobjects','parent_string');
    $this->assertDatabaseTableHasNotColumn('parentobjects','dropped');
});

test('two columns were dropped', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    Schema::dropIfExists('parentobjects');
    Schema::create('parentobjects', function($table)
    {
        $table->integer('id');
        $table->integer('parent_int');
        $table->string('parent_string');
        $table->integer('dropped');
        $table->integer('dropped2');
        $table->primary('id');
    });
    Schema::dropIfExists('parentobjects_parent_sarray');
    Schema::create('parentobjects_parent_sarray', function($table)
    {
        $table->integer('container_id');
        $table->integer('index');
        $table->integer('element');
    });
    
    $test->migrate();
    
    $this->assertDatabaseTableHasColumn('parentobjects','parent_string');
    $this->assertDatabaseTableHasNotColumn('parentobjects','dropped');
});

test('column was added', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    Schema::dropIfExists('parentobjects');
    Schema::create('parentobjects', function($table)
    {
        $table->integer('id');
        $table->integer('parent_int');
        $table->primary('id');
    });
    Schema::dropIfExists('parentobjects_parent_sarray');
    Schema::create('parentobjects_parent_sarray', function($table)
    {
        $table->integer('container_id');
        $table->integer('index');
        $table->integer('element');
    });
    
    $test->migrate();
    
    $this->assertDatabaseTableHasColumn('parentobjects','parent_string');
});

test('array column was dropped', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    Schema::dropIfExists('parentobjects');
    Schema::create('parentobjects', function($table)
    {
        $table->integer('id');
        $table->integer('parent_int');
        $table->string('parent_string');
        $table->primary('id');
    });
    Schema::dropIfExists('parentobjects_parent_sarray');
    Schema::create('parentobjects_parent_sarray', function($table)
    {
        $table->integer('container_id');
        $table->integer('index');
        $table->integer('element');
    });
    Schema::dropIfExists('parentobjects_droparray');
    Schema::create('parentobjects_droparray', function($table)
    {
        $table->integer('container_id');
        $table->integer('index');
        $table->integer('element');
    });
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
    $this->assertDatabaseHasNotTable('parentobjects_droparray');
});

test('array column was added', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    Schema::dropIfExists('parentobjects');
    Schema::create('parentobjects', function($table)
    {
        $table->integer('id');
        $table->integer('parent_int');
        $table->string('parent_string');
        $table->primary('id');
    });
    Schema::dropIfExists('parentobjects_parent_sarray');
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
});

test('column type changed', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    Schema::dropIfExists('parentobjects');
    Schema::create('parentobjects', function($table)
    {
        $table->integer('id');
        $table->string('parent_int');
        $table->string('parent_string');
        $table->primary('id');
    });
    Schema::dropIfExists('parentobjects_parent_sarray');
    Schema::create('parentobjects_parent_sarray', function($table)
    {
        $table->integer('container_id');
        $table->integer('index');
        $table->integer('element');
    });
    
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('parentobjects','parent_int','integer');
});

test('column array type changed', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    Schema::dropIfExists('parentobjects');
    Schema::create('parentobjects', function($table)
    {
        $table->integer('id');
        $table->integer('parent_int');
        $table->string('parent_string');
        $table->primary('id');
    });
    Schema::dropIfExists('parentobjects_parent_sarray');
    Schema::create('parentobjects_parent_sarray', function($table)
    {
        $table->integer('container_id');
        $table->string('index');
        $table->integer('element');
    });
    
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','index','integer');
    
});

test('column array index type changed', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ParentObject::getExpectedStructure());
    ParentObject::prepareDatabase($this);
    
    Schema::dropIfExists('parentobjects');
    Schema::create('parentobjects', function($table)
    {
        $table->integer('id');
        $table->integer('parent_int');
        $table->string('parent_string');
        $table->primary('id');
    });
    Schema::dropIfExists('parentobjects_parent_sarray');
    Schema::create('parentobjects_parent_sarray', function($table)
    {
        $table->integer('container_id');
        $table->integer('index');
        $table->string('element');
    });
    
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','element','integer');
    
});

