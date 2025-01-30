<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;

uses(SimpleTestCase::class);

test('Dummy structure is returned as expected', function()
{
    $test = new Dummy();
    expect(checkStdClasses(Dummy::getExpectedStructure(), $test->getStructure()))->toBe(true);    
});