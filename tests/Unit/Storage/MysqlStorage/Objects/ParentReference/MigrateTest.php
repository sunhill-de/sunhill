<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Illuminate\Support\Facades\Schema;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentReference;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Migrate with nothing to do', function()
{
    $test = prepareStorage(ParentReference::class, $this);
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('parentreferences');
    $this->assertDatabaseHasTable('parentreferences_parent_rarray');
    
    $this->assertDatabaseTableColumnIsType('parentreferences', 'parent_reference', 'integer');
    
    $this->assertDatabaseTableColumnIsType('parentreferences_parent_rarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('parentreferences_parent_rarray','index','integer');
    $this->assertDatabaseTableColumnIsType('parentreferences_parent_rarray','element','integer');
})->group('migrate');

test('Migrate fresh', function()
{
    $test = prepareStorage(ParentReference::class, $this);
    
    Schema::drop('parentreferences');
    Schema::drop('parentreferences_parent_rarray');
    
    $test->migrate();
    
    $this->assertDatabaseHasTable('parentreferences');
    $this->assertDatabaseHasTable('parentreferences_parent_rarray');
    
    $this->assertDatabaseTableColumnIsType('parentreferences', 'parent_reference', 'integer');
    
    $this->assertDatabaseTableColumnIsType('parentreferences_parent_rarray','container_id','integer');
    $this->assertDatabaseTableColumnIsType('parentreferences_parent_rarray','index','integer');
    $this->assertDatabaseTableColumnIsType('parentreferences_parent_rarray','element','integer');
})->group('migrate');
