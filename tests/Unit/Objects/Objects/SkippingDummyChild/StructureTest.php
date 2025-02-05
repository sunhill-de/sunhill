<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;

uses(SimpleTestCase::class);

test('SkippingDummyGrandchild structure is returned as expected', function()
{
    $test = new SkippingDummyChild();
    expect(checkStdClasses(SkippingDummyChild::getExpectedStructure(), $test->getStructure()))->toBe(true);    
});