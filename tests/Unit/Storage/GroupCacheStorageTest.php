<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Storage\Exceptions\FieldNotAvaiableException;
use Sunhill\Properties\Tests\TestSupport\Storages\DummySimpleWriteableStorage;
use Sunhill\Properties\Tests\TestSupport\Storages\DummyGroupCacheStorage;
use Illuminate\Support\Facades\Cache;
use Sunhill\Properties\Storage\Exceptions\CacheIDNotSetException;


test('read value', function () {
    Cache::flush();

    $test = new DummyGroupCacheStorage();
    $test->setCacheID('teststorage');
    expect($test->getValue('keyA'))->toEqual('ValueA');
    expect($test::$call_count)->toEqual(1);

    $test2 = new DummyGroupCacheStorage();
    $test2->setCacheID('teststorage');
    expect($test2->getValue('keyB'))->toEqual('ValueB');
    expect($test::$call_count)->toEqual(1);
});

test('no cache i d set', function () {
    $this->expectException(CacheIDNotSetException::class);

    $test = new DummyGroupCacheStorage();
    $test->getValue('keyA');
});
