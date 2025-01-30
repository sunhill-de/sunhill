<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Storage\PersistentPoolStorage;

uses(SimpleTestCase::class);

test('Load a dummy', function()
{
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('load')->once()->with(123);
    $storage->shouldReceive('setStructure')->once();
    $test = new Dummy();
    $test->setStorage($storage);
    $test->load(123);
});