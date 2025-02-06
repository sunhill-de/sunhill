<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\ChildObject;

uses(SunhillDatabaseTestCase::class);

test('load a ChildObject', function()
{
    ChildObject::prepareDatabase($this);
    $test = new ChildObject();
    $test->load(9);
    expect($test->parent_int)->toBe(333);
    expect($test->parent_string)->toBe('CCC');
    expect($test->parent_sarray[1])->toBe(31);
    expect($test->child_int)->toBe(212);
    expect($test->child_string)->toBe('BCD');
    expect($test->child_sarray[1])->toBe(210);
});

