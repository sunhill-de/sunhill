<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Properties\ChildRecordProperty;

uses(SimpleTestCase::class);

test('inheriteted embedded properties', function()
{
    ChildRecordProperty::setInclusion('embed');
    
    $test = new ChildRecordProperty();
    expect($test->hasElement('child_int'))->toBe(true);
    expect($test->hasElement('parent_int'))->toBe(true);
});

test('inherited included properties', function()
{
    ChildRecordProperty::setInclusion('include');    

    $test = new ChildRecordProperty();
    expect($test->hasElement('child_int'))->toBe(true);
    expect($test->hasElement('parent_int'))->toBe(true);    
});