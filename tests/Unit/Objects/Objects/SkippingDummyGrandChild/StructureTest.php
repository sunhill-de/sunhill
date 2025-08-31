<?php
/**
 * @file StructureTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: dependent of helper functions
 */

use Sunhill\Tests\SunhillLaravelTestCase;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;

uses(SunhillLaravelTestCase::class);

test('SkippingDummyGrandchild structure is returned as expected', function()
{
    $test = new SkippingDummyGrandChild();
    expect(checkStdClasses(SkippingDummyGrandChild::getExpectedStructure(), $test->getStructure()))->toBe(true);    
});