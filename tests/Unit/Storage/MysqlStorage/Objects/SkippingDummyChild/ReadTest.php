<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;

uses(SunhillDatabaseTestCase::class);

test('Read a skippingdummychild from database', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(SkippingDummyChild::getExpectedStructure());
    SkippingDummyChild::prepareDatabase($this);
    $test->load(14);
    
    expect($test->getValue('dummyint'))->toBe(987);
});

