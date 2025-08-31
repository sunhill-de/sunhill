<?php

/**
 * @file AccessTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: no, depends on helper functions
 */

use Sunhill\Tests\SunhillLaravelTestCase;
use Sunhill\Tests\TestSupport\Objects\DummyChild;

uses(SunhillLaravelTestCase::class);

test('DummyChild structure is returned as expected', function () {
    $test = new DummyChild;
    expect(checkStdClasses(DummyChild::getExpectedStructure(), $test->getStructure()))->toBe(true);
});
