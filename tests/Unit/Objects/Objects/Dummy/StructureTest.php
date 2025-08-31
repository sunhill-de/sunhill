<?php
/**
 * @file StructureTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: no, dependent on helper functions
 */

use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\SunhillLaravelTestCase;

uses(SunhillLaravelTestCase::class);

test('Dummy structure is returned as expected', function()
{
    $test = new Dummy();
    expect(checkStdClasses(Dummy::getExpectedStructure(), $test->getStructure()))->toBe(true);    
});