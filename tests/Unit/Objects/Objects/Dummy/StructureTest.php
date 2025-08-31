<?php

/**
 * @file StructureTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: no, dependent on helper functions
 */

use Sunhill\Tests\SunhillLaravelTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;

uses(SunhillLaravelTestCase::class);

test('Dummy structure is returned as expected', function () {
    $test = new Dummy;
    expect(checkStdClasses(Dummy::getExpectedStructure(), $test->getStructure()))->toBe(true);
});
