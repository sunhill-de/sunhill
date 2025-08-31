<?php

use Sunhill\Tests\SunhillLaravelTestCase;
use Sunhill\Tests\TestSupport\Objects\ChildObject;

uses(SunhillLaravelTestCase::class);

test('ChildObject structure is returned as expected', function () {
    $test = new ChildObject;
    expect(checkStdClasses(ChildObject::getExpectedStructure(), $test->getStructure()))->toBe(true);
});
