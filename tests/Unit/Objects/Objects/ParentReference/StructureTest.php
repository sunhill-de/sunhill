<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;
use Sunhill\Tests\TestSupport\Objects\ParentReference;

uses(SimpleTestCase::class);

test('ParentReference structure is returned as expected', function()
{
    $test = new ParentReference();
    $one = ParentReference::getExpectedStructure();
    $two = $test->getStructure();
    expect(checkStdClasses(ParentReference::getExpectedStructure(), $test->getStructure()))->toBe(true);    
});