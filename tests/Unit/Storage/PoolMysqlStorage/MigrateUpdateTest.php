<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\PoolMysqlStorage\PoolMysqlStorage;

uses(SunhillDatabaseTestCase::class);

require_once('PrepareStorage.php');

test('column was dropped', function()
{
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
    
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'parentobject', false));
    $test->migrate();
    
    $this->assertDatabaseTableHasColumn('parentobjects','parent_string');
    $this->assertDatabaseTableHasNotColumn('parentobjects','dropped');
});

test('two columns were dropped', function()
{
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
    
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'parentobject', false));
    $test->migrate();
    
    $this->assertDatabaseTableHasColumn('parentobjects','parent_string');
    $this->assertDatabaseTableHasNotColumn('parentobjects','dropped');
});

test('column was added', function()
{
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
    
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'parentobject', false));
    $test->migrate();
    
    $this->assertDatabaseTableHasColumn('parentobjects','parent_string');
});

test('array column was dropped', function()
{
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
    
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'parentobject', false));
    $test->migrate();
    
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
    $this->assertDatabaseHasNotTable('parentobjects_droparray');
});

test('array column was added', function()
{
    Schema::dropIfExists('parentobjects');
    Schema::create('parentobjects', function($table)
    {
        $table->integer('id');
        $table->integer('parent_int');
        $table->string('parent_string');
        $table->primary('id');
    });
    Schema::dropIfExists('parentobjects_parent_sarray');
    
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'parentobject', false));
    $test->migrate();
    
    $this->assertDatabaseHasTable('parentobjects_parent_sarray');
});

test('column type changed', function()
{
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

    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'parentobject', false));
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('parentobjects','parent_int','integer');
});

test('column array type changed', function()
{
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
    
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'parentobject', false));
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','index','integer');
    
});

test('column array index type changed', function()
{
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
    
    $test = new PoolMysqlStorage();
    $test->setStructure(prepareStorage($this, 'parentobject', false));
    $test->migrate();
    
    $this->assertDatabaseTableColumnIsType('parentobjects_parent_sarray','element','integer');
    
});

