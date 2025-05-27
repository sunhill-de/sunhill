<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;

uses(SunhillDatabaseTestCase::class);

test('Load an ArrayOnlyChildObject from database', function()
{
    ArrayOnlyChildObject::prepareDatabase($this);
    $test = new ArrayOnlyChildObject();    
    $test->load(20);
    expect($test->parent_int)->toBe(5555);
    expect($test->parent_string)->toBe('ERE');
    expect($test->parent_sarray[1])->toBe(41);
    expect($test->child_sarray[1])->toBe(2100);
});