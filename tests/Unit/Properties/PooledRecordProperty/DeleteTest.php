<?php

/**
 * @file DeleteTest.php
 * tests: /src/Properties/PooledRecordProperty.php
 * free of dependent units: yes
 */

use Sunhill\Properties\PooledRecordProperty;
use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

test('delete calls storage delete()', function () {
    $storage = \Mockery::mock(PersistentPoolStorage::class);
    $storage->shouldReceive('delete')->once()->with(1);
    $test = new PooledRecordProperty;
    $test->setStorage($storage);
    $test->delete(1);
});
