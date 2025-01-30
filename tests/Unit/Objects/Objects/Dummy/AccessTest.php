<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;

uses(SimpleTestCase::class);

test('read of dummy value', function()
{
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('getValue')->with('dummyint')->andReturn(123);
    $test = new Dummy();
    $test->setStorage($storage);
    
    
});