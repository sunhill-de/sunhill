<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\DummyChild;

uses(SunhillDatabaseTestCase::class);

test('Load a dummychild from database', function()
{
    DummyChild::prepareDatabase($this);
    $test = new DummyChild();    
    $test->load(13);
    expect($test->dummyint)->toBe(999);
    expect($test->dummychildint)->toBe(919);
});