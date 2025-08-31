<?php

/**
 * @file AccessTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: yes
 */

use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\ParentObject;

uses(SunhillSimpleTestCase::class);

test('read of Parent Object value', function () {
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('getValue')->with('parent_int')->andReturn(123);
    $storage->shouldReceive('getIsInitialized')->with('parent_int')->andReturn(true);
    $storage->shouldReceive('getValue')->with('parent_string')->andReturn('ATA');
    $storage->shouldReceive('getIsInitialized')->with('parent_string')->andReturn(true);
    $storage->shouldReceive('getIndexedValue')->once()->with('parent_sarray', 1)->andReturn(2);
    $storage->shouldReceive('getOffsetExists')->with('parent_sarray', 1)->andReturn(true);
    $storage->shouldReceive('getIsInitialized')->with('parent_sarray')->andReturn(true);
    $storage->shouldReceive('getValue')->with('_attributes')->andReturn([]);
    $test = new ParentObject;
    $test->setStorage($storage);

    expect($test->parent_int)->toBe(123);
    expect($test->parent_string)->toBe('ATA');
    expect($test->parent_sarray[1])->toBe(2);
});
