<?php

/**
 * @file StructureTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: is dependend on helper functions
 */

use Sunhill\Tests\TestSupport\Objects\ArrayOnlyChildObject;
use Sunhill\Tests\SunhillLaravelTestCase;

uses(SunhillLaravelTestCase::class);

test('ArrayOnlyChildObject structure is returned as expected', function()
{
    $test = new ArrayOnlyChildObject();
    expect(checkStdClasses(ArrayOnlyChildObject::getExpectedStructure(), $test->getStructure()))->toBe(true);    
});