<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;

uses(SimpleTestCase::class);

test('ArrayOnlyChildObject structure is returned as expected', function()
{
    $test = new ArrayOnlyChildObject();
    expect(checkStdClasses(ArrayOnlyChildObject::getExpectedStructure(), $test->getStructure()))->toBe(true);    
});