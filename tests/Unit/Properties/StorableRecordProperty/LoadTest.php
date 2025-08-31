<?php
/**
 * @file LoadTest.php
 * tests: /src/Properties/StorableRecordProperty.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Properties\Exceptions\WrongStorageSetException;
use Sunhill\Storage\PersistentSingleStorage;
use Sunhill\Properties\StorableRecordProperty;

uses(SunhillSimpleTestCase::class);

test('Load calls storage load()', function()
{
    $storage = \Mockery::mock(PersistentSingleStorage::class);
    $storage->shouldReceive('load')->once();
    $test = new StorableRecordProperty(); 
    $test->setStorage($storage);
    $test->load();
});

it('Fails with wrong storage set', function()
{
    $storage = \Mockery::mock(AbstractStorage::class);
    $test = new StorableRecordProperty();
    $test->setStorage($storage);
    $test->load();
})->throws(WrongStorageSetException::class);
