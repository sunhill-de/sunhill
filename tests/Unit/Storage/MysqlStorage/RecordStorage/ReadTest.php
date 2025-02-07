<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\Exceptions\StorageTableMissingException;
use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\Exceptions\IDNotFoundException;
use Sunhill\Storage\Exceptions\InvalidIDException;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\ChildObject;

require_once('PrepareStorage.php');

uses(SunhillDatabaseTestCase::class);

it('fails when using a wrong id type', function()
{
    $test = new MysqlObjectStorage();
    
    $test->load('A');     
})->throws(InvalidIDException::class);

it('fails when a table is missing', function()
{
    $test = new MysqlObjectStorage();
    
    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    Schema::drop('parentobjects_parent_sarray');
    
    $test->load(12);
})->throws(StorageTableMissingException::class);

it('fails when reading an unknown id', function()
{
    $test = new MysqlObjectStorage();

    $test->setStructure(ChildObject::getExpectedStructure());
    ChildObject::prepareDatabase($this);
    
    $test->load(999);
})->throws(IDNotFoundException::class);
