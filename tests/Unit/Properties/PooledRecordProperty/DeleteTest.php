<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Properties\PooledRecordProperty;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Properties\Exceptions\WrongStorageSetException;

uses(SimpleTestCase::class);

test('delete calls storage delete()', function()
{
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('delete')->once()->with(1);
    $test = new PooledRecordProperty(); 
    $test->setStorage($storage);
    $test->delete(1);
});

