<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;

uses(SunhillDatabaseTestCase::class);

test('Load a SkippingDummyGrandChild from database', function()
{
    SkippingDummyGrandChild::prepareDatabase($this);
    $test = new SkippingDummyGrandChild();    
    $test->load(16);
    expect($test->dummyint)->toBe(976);
    expect($test->dummygrandchildint)->toBe(9111);
});