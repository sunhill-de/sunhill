<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\ParentReference;

uses(SunhillDatabaseTestCase::class);

test('Load a ParentReference from database (both references refer something)', function()
{
    ParentReference::prepareDatabase($this);
    $test = new ParentReference();    
    $test->load(17);
    expect($test->parent_int)->toBe(1111);
    expect($test->parent_reference->dummyint)->toBe(123);
    expect($test->parent_rarray[2]->dummyint)->toBe(234);
});