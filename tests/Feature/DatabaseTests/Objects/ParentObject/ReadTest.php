<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SunhillDatabaseTestCase::class);

test('load a ParentObject', function()
{
    ParentObject::prepareDatabase($this);   
    $test = new ParentObject();
    $test->load(7);
    expect($test->parent_int)->toBe(111);
    expect($test->parent_string)->toBe('AAA');
    expect($test->parent_sarray[1])->toBe(11);
});

