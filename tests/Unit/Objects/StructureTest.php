<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\DummyChild;

uses(SimpleTestCase::class);

test('getStructure()', function($classname)
{
    $test = new $classname();
    expect(checkStdClasses($classname::getExpectedStructure(), $test->getStructure()))->toBe(true);
})->with([
    [Dummy::class]
]);
