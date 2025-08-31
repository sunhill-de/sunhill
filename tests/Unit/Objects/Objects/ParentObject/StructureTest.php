<?php

/**
 * @file AccessTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: no, depends on helper functions
 */

use Sunhill\Tests\SunhillLaravelTestCase;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SunhillLaravelTestCase::class);

test('ParentObject structure is returned as expected', function () {
    $test = new ParentObject;
    expect(checkStdClasses(ParentObject::getExpectedStructure(), $test->getStructure()))->toBe(true);
});
