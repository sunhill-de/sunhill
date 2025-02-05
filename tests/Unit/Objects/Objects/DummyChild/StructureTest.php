<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\DummyChild;

uses(SimpleTestCase::class);

test('DummyChild structure is returned as expected', function()
{
    $test = new DummyChild();
    expect(checkStdClasses(DummyChild::getExpectedStructure(), $test->getStructure()))->toBe(true);    
});