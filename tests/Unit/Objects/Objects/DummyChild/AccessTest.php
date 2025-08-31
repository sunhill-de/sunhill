<?php
/**
 * @file AccessTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Tests\TestSupport\Objects\DummyChild;

uses(SunhillSimpleTestCase::class);

test('read of dummy child value', function()
{
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('getValue')->with('dummyint')->andReturn(123);
    $storage->shouldReceive('getIsInitialized')->with('dummyint')->andReturn(true);
    $storage->shouldReceive('getValue')->with('dummychildint')->andReturn(234);
    $storage->shouldReceive('getIsInitialized')->with('dummychildint')->andReturn(true);
    $storage->shouldReceive('getValue')->with('_attributes')->andReturn([]);
    $test = new DummyChild();
    $test->setStorage($storage);
    
    expect($test->dummyint)->toBe(123);
    expect($test->dummychildint)->toBe(234);
});