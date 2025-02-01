<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Properties\PooledRecordProperty;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Properties\Exceptions\WrongStorageSetException;

uses(SimpleTestCase::class);

test('Load calls storage load()', function()
{
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('load')->once()->with(1);
    $storage->shouldReceive('setStructure')->once();
    $test = new PooledRecordProperty(); 
    $test->setStorage($storage);
    $test->load(1);
});

test('Loading sets id', function()
{
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('load')->with(1);
    $storage->shouldReceive('getID')->once()->andReturn(1);
    $storage->shouldReceive('setStructure')->once();
    $test = new PooledRecordProperty();
    $test->setStorage($storage);
    $test->load(1);
    expect($test->getID())->toBe(1);
});

it('Fails with wrong storage set', function()
{
    $storage = \Mockery::mock(AbstractStorage::class);
    $test = new PooledRecordProperty();
    $test->setStorage($storage);
    $test->load(1);
})->throws(WrongStorageSetException::class);
