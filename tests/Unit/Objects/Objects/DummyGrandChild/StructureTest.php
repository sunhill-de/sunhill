<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;

uses(SimpleTestCase::class);

test('DummyGrandchild structure is returned as expected', function()
{
    $test = new DummyGrandChild();
    expect(checkStdClasses(DummyGrandChild::getExpectedStructure(), $test->getStructure()))->toBe(true);    
});