<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;

uses(SunhillDatabaseTestCase::class);

test('Read a dummygrandchild from database', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(DummyGrandChild::getExpectedStructure());
    DummyGrandChild::prepareDatabase($this);
    $test->load(15);
    
    expect($test->getValue('dummyint'))->toBe(986);
    expect($test->getValue('dummychildint'))->toBe(979);
    expect($test->getValue('dummygrandchildint'))->toBe(911);
});

