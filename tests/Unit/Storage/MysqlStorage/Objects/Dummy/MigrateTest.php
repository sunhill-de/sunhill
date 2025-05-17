<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Illuminate\Support\Facades\Schema;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Migrate with nothing to do', function()
{
    $test = prepareStorage(Dummy::class, $this);
    
    $test->migrate();
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableHasColumn('dummies','dummyint');
    $this->assertDatabaseTableHasColumn('dummies','id');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');    
})->group('migrate');

test('Migrate fresh for dummy', function()
{
    $test = prepareStorage(Dummy::class, $this);
    
    Schema::drop('dummies');
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableHasColumn('dummies','dummyint');
    $this->assertDatabaseTableHasColumn('dummies','id');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
})->group('migrate');

test('Migrate with a field dropped', function()
{
    $test = prepareStorage(Dummy::class, $this);
    
    Schema::drop('dummies');
    Schema::create('dummies', function($table) {
       $table->integer('id')->primary();
       $table->integer('dummyint');
       $table->integer('dropped');
    });
    
    $test->migrate();
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableHasColumn('dummies','dummyint');
    $this->assertDatabaseTableHasNotColumn('dummies','dropped');
    $this->assertDatabaseTableHasColumn('dummies','id');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
})->group('migrate');

test('Migrate with a field added', function()
{
    $test = prepareStorage(Dummy::class, $this);
    
    Schema::drop('dummies');
    Schema::create('dummies', function($table) 
    {
        $table->integer('id')->primary();
    });
        
    $test->migrate();
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableHasColumn('dummies','dummyint');
    $this->assertDatabaseTableHasColumn('dummies','id');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
})->group('migrate');

test('Migrate with a field type changed', function()
{
    $test = prepareStorage(Dummy::class, $this);
    
    Schema::drop('dummies');
    Schema::create('dummies', function($table) {
        $table->string('id')->primary();
    });
        
        $test->migrate();
        $this->assertDatabaseHasTable('dummies');
        $this->assertDatabaseTableHasColumn('dummies','dummyint');
        $this->assertDatabaseTableHasColumn('dummies','id');
        $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
        $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
})->group('migrate');
