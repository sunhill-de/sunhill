<?php

/**
 * @file StructureTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: dependent of helper functions
 */

use Sunhill\Tests\SunhillLaravelTestCase;
use Sunhill\Tests\TestSupport\Objects\ParentReference;

uses(SunhillLaravelTestCase::class);

test('ParentReference structure is returned as expected', function () {
    $test = new ParentReference;
    $one = ParentReference::getExpectedStructure();
    $two = $test->getStructure();
    expect(checkStdClasses(ParentReference::getExpectedStructure(), $test->getStructure()))->toBe(true);
});
