<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\Exceptions\StorageTableMissingException;
use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\Exceptions\IDNotFoundException;
use Sunhill\Storage\Exceptions\InvalidIDException;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;

require_once('PrepareStorage.php');

uses(SunhillDatabaseTestCase::class);

test('fails when using a wrong id type', function()
{
    $test = new MysqlObjectStorage();
    $test->load('A');     
})->throws(InvalidIDException::class);

it('fails when a table is missing', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    Schema::drop('parentobjects_parent_sarray');
    $test->load(12);
})->throws(StorageTableMissingException::class);

it('fails when reading an unknown id', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(prepareStorage($this, 'childobject'));
    $test->load(999);
})->throws(IDNotFoundException::class);
