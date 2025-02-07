<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\Exceptions\StorageTableMissingException;
use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\Exceptions\IDNotFoundException;
use Sunhill\Storage\Exceptions\InvalidIDException;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\ChildObject;

require_once('PrepareStorage.php');

uses(SunhillDatabaseTestCase::class);

test('fails when using a wrong id type', function()
{
    $test = new MysqlObjectStorage();
    $test->delete('A');     
})->throws(InvalidIDException::class);

it('fails when a table is missing', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    Schema::drop('parentobjects_parent_sarray');
    
    $test->delete(12);
})->throws(StorageTableMissingException::class);

it('fails when deleting an unknown id', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    $test->delete(999);
})->throws(IDNotFoundException::class);
