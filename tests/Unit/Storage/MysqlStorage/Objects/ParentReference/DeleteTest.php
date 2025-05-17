<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ParentObject;
use Sunhill\Tests\TestSupport\Objects\ParentReference;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Delete a parentreference with array', function()
{
    $test = prepareStorage(ParentReference::class, $this);
    
    $test->delete(17);
    
    $this->assertDatabaseMissing('parentobjects',['id'=>17]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>17]);
})->group('delete');

test('Delete a parentreference with empty array', function()
{
    $test = prepareStorage(ParentReference::class, $this);
    
    $test->delete(19);
    
    $this->assertDatabaseMissing('parentobjects',['id'=>19]);
    $this->assertDatabaseMissing('parentobjects_parent_sarray',['container_id'=>19]);
})->group('delete');

