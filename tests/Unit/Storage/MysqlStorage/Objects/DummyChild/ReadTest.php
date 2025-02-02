<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\DummyChild;

uses(SunhillDatabaseTestCase::class);

test('Read a dummychild from database', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(DummyChild::getExpectedStructure());
    DummyChild::prepareDatabase($this);
    $test->load(13);
    
    expect($test->getValue('dummyint'))->toBe(999);
    expect($test->getValue('dummychildint'))->toBe(919);
});

