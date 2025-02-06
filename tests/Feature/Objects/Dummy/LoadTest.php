<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;

uses(SunhillDatabaseTestCase::class);

test('Load a dummy from database', function()
{
    Dummy::prepareDatabase($this);
    $test = new Dummy();    
    $test->load(1);
    expect($test->dummyint)->toBe(123);
});