<?php
/**
 * @file StoreTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: yes
 */

use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

test('Commit of a non initialized dummy works', function()
{
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('getID')->once()->andReturn(null);
    $storage->shouldReceive('getIsInitialized')->andReturn(true);
    $storage->shouldReceive('setValue'); // For the timestamps
    $storage->shouldReceive('commit')->once();
    $storage->shouldReceive('getValue')->with('_attributes')->andReturn([]);
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
    $storage->shouldReceive('getValue')->with('_attributes')->andReturn([]);
    $test = new Dummy();
    $test->setStorage($storage);
    
    $test->commit();
});