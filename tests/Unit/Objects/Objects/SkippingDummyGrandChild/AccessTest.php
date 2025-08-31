<?php

/**
 * @file AccessTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: yes
 */

use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;

uses(SunhillSimpleTestCase::class);

test('read of dummy grand child value', function () {
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('getValue')->with('dummyint')->andReturn(123);
    $storage->shouldReceive('getIsInitialized')->with('dummyint')->andReturn(true);
    $storage->shouldReceive('getValue')->with('dummygrandchildint')->andReturn(345);
    $storage->shouldReceive('getValue')->with('_attributes')->andReturn([]);
    $storage->shouldReceive('getIsInitialized')->with('dummygrandchildint')->andReturn(true);
    $test = new SkippingDummyGrandChild;
    $test->setStorage($storage);

    expect($test->dummyint)->toBe(123);
    expect($test->dummygrandchildint)->toBe(345);
});
