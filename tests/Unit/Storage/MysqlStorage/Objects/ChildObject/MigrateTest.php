<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\ChildObject;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Illuminate\Support\Facades\Schema;

uses(SunhillDatabaseTestCase::class);

test('Migrate fresh for childobject', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    Schema::drop('parentobjects');
    Schema::drop('childobjects');
    
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