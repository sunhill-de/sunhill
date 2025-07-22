<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\BigParent;

uses(SimpleTestCase::class);

test('BigParent structure is returned as expected', function()
{
    $test = new BigParent();
    expect($test->getStructure())->toEqual(BigParent::getExpectedStructure());    
})->group('structure');