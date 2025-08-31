<?php
/**
 * @file AccessTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillLaravelTestCase;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;

uses(SunhillLaravelTestCase::class);

test('SkippingDummyGrandchild structure is returned as expected', function()
{
    $test = new SkippingDummyChild();
    expect(checkStdClasses(SkippingDummyChild::getExpectedStructure(), $test->getStructure()))->toBe(true);    
});