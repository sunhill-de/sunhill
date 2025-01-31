<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;

uses(SimpleTestCase::class);

test('Commit of a non initialized dummy works', function()
{
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('getID')->once()->andReturn(null);
    $storage->shouldReceive('getIsInitialized')->andReturn(true);
    $storage->shouldReceive('setValue'); // For the timestamps
    $storage->shouldReceive('commit')->once();
    $test = new Dummy();
    $test->setStorage($storage);
    
    $test->commit();
});

test('Commit of a initialized dummy works', function()
{
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('getID')->once()->andReturn(1);
    $storage->shouldReceive('getIsInitialized')->andReturn(true);
    $storage->shouldReceive('setValue'); // For the timestamps
    $storage->shouldReceive('commit')->once();
    $test = new Dummy();
    $test->setStorage($storage);
    
    $test->commit();
});