<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;

uses(SunhillDatabaseTestCase::class);

test('Load a SkippingDummyChild from database', function()
{
    SkippingDummyChild::prepareDatabase($this);
    $test = new SkippingDummyChild();    
    $test->load(14);
    expect($test->dummyint)->toBe(987);
});