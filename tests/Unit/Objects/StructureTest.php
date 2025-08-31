<?php

/**
 * @file StructureTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: not fully (uses helper functions)
 */

use Sunhill\Tests\SunhillLaravelTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;

uses(SunhillLaravelTestCase::class); // It uses helper functions

test('getStructure()', function ($classname) {
    $test = new $classname;
    expect(checkStdClasses($classname::getExpectedStructure(), $test->getStructure()))->toBe(true);
})->with([
    [Dummy::class],
]);
