<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\ChildObject;

uses(SimpleTestCase::class);

test('ChildObject structure is returned as expected', function()
{
    $test = new ChildObject();
    expect(checkStdClasses(ChildObject::getExpectedStructure(), $test->getStructure()))->toBe(true);    
});