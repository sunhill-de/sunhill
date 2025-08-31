<?php

/**
 * @file AccessTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: no, depends on helper functions
 */

use Sunhill\Tests\SunhillLaravelTestCase;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;

uses(SunhillLaravelTestCase::class);

test('DummyGrandchild structure is returned as expected', function () {
    $test = new DummyGrandChild;
    expect(checkStdClasses(DummyGrandChild::getExpectedStructure(), $test->getStructure()))->toBe(true);
});
