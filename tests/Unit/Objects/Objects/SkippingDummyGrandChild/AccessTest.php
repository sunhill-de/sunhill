<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;
use Sunhill\Tests\TestSupport\Objects\SkippingDummyGrandChild;

uses(SimpleTestCase::class);

test('read of dummy grand child value', function()
{
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('getValue')->with('dummyint')->andReturn(123);
    $storage->shouldReceive('getIsInitialized')->with('dummyint')->andReturn(true);
    $storage->shouldReceive('getValue')->with('dummygrandchildint')->andReturn(345);
    $storage->shouldReceive('getIsInitialized')->with('dummygrandchildint')->andReturn(true);
    $test = new SkippingDummyGrandChild();
    $test->setStorage($storage);
    
    expect($test->dummyint)->toBe(123);
    expect($test->dummygrandchildint)->toBe(345);
});