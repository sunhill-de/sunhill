<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;

uses(SunhillDatabaseTestCase::class);

test('Read a skippingdummygrandchild from database', function()
{
    $test = new MysqlObjectStorage();
    $test->setStructure(SkippingDummyGrandChild::getExpectedStructure());
    SkippingDummyGrandChild::prepareDatabase($this);
    $test->load(16);
    
    expect($test->getValue('dummyint'))->toBe(976);
    expect($test->getValue('dummygrandchildint'))->toBe(9111);
});

