<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Delete a parentobject with array', function()
{
    $test = prepareStorage(ParentObject::class, $this);
    
    $test->delete(7);
    
    $this->assertDatabaseMissing('parentobjects',['id'=>7]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>7]);
})->group('delete');

test('Delete a parentobject with empty array', function()
{
    $test = prepareStorage(ParentObject::class, $this);
    
    $test->delete(8);
    
    $this->assertDatabaseMissing('parentobjects',['id'=>8]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>8]);
})->group('delete');

