<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;

uses(SimpleTestCase::class);

test('read of dummy grand child value', function()
{
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('getValue')->with('dummyint')->andReturn(123);
    $storage->shouldReceive('getIsInitialized')->with('dummyint')->andReturn(true);
    $storage->shouldReceive('getValue')->with('dummychildint')->andReturn(234);
    $storage->shouldReceive('getIsInitialized')->with('dummychildint')->andReturn(true);
    $storage->shouldReceive('getValue')->with('dummygrandchildint')->andReturn(345);
    $storage->shouldReceive('getIsInitialized')->with('dummygrandchildint')->andReturn(true);
    $test = new DummyGrandChild();
    $test->setStorage($storage);
    
    expect($test->dummyint)->toBe(123);
    expect($test->dummychildint)->toBe(234);
    expect($test->dummygrandchildint)->toBe(345);
});