<?php

/**
 * @file AccessTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: yes
 */

use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyChild;

uses(SunhillSimpleTestCase::class);

test('read of skipping dummy grand child value', function () {
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('getValue')->with('dummyint')->andReturn(123);
    $storage->shouldReceive('getIsInitialized')->with('dummyint')->andReturn(true);
    $storage->shouldReceive('getValue')->with('_attributes')->andReturn([]);
    $test = new SkippingDummyChild;
    $test->setStorage($storage);

    expect($test->dummyint)->toBe(123);
});
