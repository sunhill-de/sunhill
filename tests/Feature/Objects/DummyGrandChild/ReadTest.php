<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;

uses(SunhillDatabaseTestCase::class);

test('Load a DummyGrandChild from database', function()
{
    DummyGrandChild::prepareDatabase($this);
    $test = new DummyGrandChild();    
    $test->load(15);
    expect($test->dummyint)->toBe(986);
    expect($test->dummychildint)->toBe(979);
    expect($test->dummygrandchildint)->toBe(911);
});