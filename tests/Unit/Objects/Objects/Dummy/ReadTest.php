<?php

/**
 * @file ReadTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: yes
 */

use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;

uses(SunhillSimpleTestCase::class);

test('Load a dummy', function () {
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('load')->once()->with(123);
    $storage->shouldReceive('setStructure')->once();
    $storage->shouldReceive('getValue')->with('_attributes')->andReturn([]);
    $storage->shouldReceive('getValue')->with('_tags')->andReturn([]);
    $storage->shouldReceive('getClassOf')->with(123)->andReturn('Dummy');
    $test = new Dummy;
    $test->setStorage($storage);
    $test->load(123);
});
