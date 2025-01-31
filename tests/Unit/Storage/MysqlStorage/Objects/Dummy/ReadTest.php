<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\PoolMysqlStorage\PoolMysqlStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;

uses(SunhillDatabaseTestCase::class);

test('Read a dummy from database (dummyint)', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(Dummy::getExpectedStructure());
    Dummy::prepareDatabase($this);
    $test->load(1);
    
    expect($test->getValue('dummyint'))->toBe(123);
});

test('Read a dummy from database (_tags)', function()
{
    $test = new PoolMysqlStorage();
    $test->setStructure(Dummy::getExpectedStructure());
    Dummy::prepareDatabase($this);
    $test->load(1);
    
    expect($test->getValue('_tags'))->toBe([1,2,3]);
});
