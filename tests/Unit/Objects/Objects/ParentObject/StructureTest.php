<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SimpleTestCase::class);

test('ParentObject structure is returned as expected', function()
{
    $test = new ParentObject();
    expect(checkStdClasses(ParentObject::getExpectedStructure(), $test->getStructure()))->toBe(true);    
});