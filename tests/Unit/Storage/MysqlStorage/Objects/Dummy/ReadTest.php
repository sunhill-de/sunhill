<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;

uses(SunhillDatabaseTestCase::class);

test('Read a dummy from database (dummyint)', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(Dummy::getExpectedStructure());
    Dummy::prepareDatabase($this);
    $test->load(1);
    
    expect($test->getValue('dummyint'))->toBe(123);
});

test('Read a dummy from database (one entry in _tags)', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(Dummy::getExpectedStructure());
    Dummy::prepareDatabase($this);
    $test->load(1);
    
    expect($test->getValue('_tags'))->toBe([1]);
});

test('Read a dummy from database (more _tags)', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(Dummy::getExpectedStructure());
    Dummy::prepareDatabase($this);
    $test->load(2);
    
    expect($test->getValue('_tags'))->toBe([1,3]);
});
