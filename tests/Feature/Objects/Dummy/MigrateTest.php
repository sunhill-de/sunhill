<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Illuminate\Support\Facades\Schema;

uses(SunhillDatabaseTestCase::class);

test('migrate Dummy with nothing to do', function()
{
    Properties::registerProperty(Dummy::class);
    Dummy::migrate();
    
    $this->assertDatabaseHasTable('dummies');
})->group('migrate');

test('migrate Dummy fresh', function()
{
    Schema::dropIfExists('dummies');
    Properties::registerProperty(Dummy::class);
    Dummy::migrate();
    
    $this->assertDatabaseHasTable('dummies');
})->group('migrate');

test('migrate Dummy with a dropped field', function()
{
    Schema::dropIfExists('dummies');
    Schema::create('dummies', function($table)
    {
       $table->integer('id');
       $table->integer('dummyint');
       $table->string('dropfield');
    });

    Properties::registerProperty(Dummy::class);
    Dummy::migrate();
    
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableHasColumn('dummies','dummyint');
    $this->assertDatabaseTableHasNotColumn('dummies','dropped');
    $this->assertDatabaseTableHasColumn('dummies','id');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
    
})->group('migrate');

test('migrate Dummy with an added field', function()
{
    Schema::dropIfExists('dummies');
    Schema::create('dummies', function($table)
    {
        $table->integer('id');
    });
    
    Properties::registerProperty(Dummy::class);
    Dummy::migrate();
    
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableHasColumn('dummies','dummyint');
    $this->assertDatabaseTableHasColumn('dummies','id');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
    
})->group('migrate');

test('Migrate with a field type changed', function()
{
    Schema::dropIfExists('dummies');
    Schema::create('dummies', function($table) 
    {
        $table->integer('id')->primary();
        $table->string('dummyint');
    });
        
    Properties::registerProperty(Dummy::class);
    Dummy::migrate();
        
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseTableHasColumn('dummies','dummyint');
    $this->assertDatabaseTableHasColumn('dummies','id');
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
})->group('migrate');

