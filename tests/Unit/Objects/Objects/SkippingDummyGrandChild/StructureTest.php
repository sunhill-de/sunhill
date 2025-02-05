<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;

uses(SimpleTestCase::class);

test('SkippingDummyGrandchild structure is returned as expected', function()
{
    $test = new SkippingDummyGrandChild();
    expect(checkStdClasses(SkippingDummyGrandChild::getExpectedStructure(), $test->getStructure()))->toBe(true);    
});