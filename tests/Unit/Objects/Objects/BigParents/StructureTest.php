<?php

use Sunhill\Tests\TestSupport\Objects\BigParent;
use Sunhill\Tests\SunhillLaravelTestCase;

uses(SunhillLaravelTestCase::class);

test('BigParent structure is returned as expected', function()
{
    $test = new BigParent();
    expect($test->getStructure())->toEqual(BigParent::getExpectedStructure());    
})->group('structure');