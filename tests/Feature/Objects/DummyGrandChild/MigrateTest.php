<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;
use Illuminate\Support\Facades\Schema;

uses(SunhillDatabaseTestCase::class);

test('migrate DummyGrandChild', function()
{
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    Properties::registerProperty(DummyGrandChild::class);
    DummyGrandChild::migrate();
    
    $this->assertDatabaseHasTable('dummies');
    $this->assertDatabaseHasTable('dummychildren');
    $this->assertDatabaseHasTable('dummygrandchildren');
});

test('Migrate with nothing to do', function()
{
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    Properties::registerProperty(DummyGrandChild::class);
    DummyGrandChild::migrate();
    
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'dummychildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'dummygrandchildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'id', 'integer');
})->group('migrate');

test('Migrate with all missing', function()
{
    Schema::drop('dummies');
    Schema::drop('dummychildren');
    Schema::drop('dummygrandchildren');

    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    Properties::registerProperty(DummyGrandChild::class);
    DummyGrandChild::migrate();
    
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'dummychildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'dummygrandchildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'id', 'integer');
})->group('migrate');

test('Migrate with grand child missing', function()
{
    Schema::drop('dummygrandchildren');
    
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    Properties::registerProperty(DummyGrandChild::class);
    DummyGrandChild::migrate();
    
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'dummychildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'dummygrandchildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'id', 'integer');
})->group('migrate');

test('Migrate with child missing', function()
{
    Schema::drop('dummychildren');
    
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    Properties::registerProperty(DummyGrandChild::class);
    DummyGrandChild::migrate();
    
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'dummychildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'dummygrandchildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'id', 'integer');
})->group('migrate');

test('Migrate with parent missing', function()
{
    
    Schema::drop('dummies');
    
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    Properties::registerProperty(DummyGrandChild::class);
    DummyGrandChild::migrate();
    
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'dummychildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'dummygrandchildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'id', 'integer');
})->group('migrate');

test('Migrate with parent field changed', function()
{
    
    Schema::drop('dummies');
    Schema::create('dummies', function($table)
    {
       $table->integer('id')->primary();
       $table->string('dummyint');
    });
    
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    Properties::registerProperty(DummyGrandChild::class);
    DummyGrandChild::migrate();
    
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'dummychildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'dummygrandchildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'id', 'integer');
})->group('migrate');

test('Migrate with child field changed', function()
{
    
    Schema::drop('dummychildren');
    Schema::create('dummychildren', function($table)
    {
        $table->integer('id')->primary();
        $table->string('dummychildint');
    });
    
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    Properties::registerProperty(DummyGrandChild::class);
    DummyGrandChild::migrate();
    
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'dummychildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'dummygrandchildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'id', 'integer');
})->group('migrate');

test('Migrate with grandchild field changed', function()
{
    
    Schema::drop('dummygrandchildren');
    Schema::create('dummygrandchildren', function($table)
    {
        $table->integer('id')->primary();
        $table->string('dummygrandchildint');
    });
    
    Properties::registerProperty(Dummy::class);
    Properties::registerProperty(DummyChild::class);
    Properties::registerProperty(DummyGrandChild::class);
    DummyGrandChild::migrate();
    
    $this->assertDatabaseTableColumnIsType('dummies', 'dummyint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummies', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'dummychildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummychildren', 'id', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'dummygrandchildint', 'integer');
    $this->assertDatabaseTableColumnIsType('dummygrandchildren', 'id', 'integer');
})->group('migrate');