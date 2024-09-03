<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Tests\TestSupport\Storages\TestAbstractStorage;
use Illuminate\Support\Facades\Cache;


test('read value', function () {
    $test = new TestAbstractStorage();
    expect($test->getValue('test'))->toEqual('TESTVALUE');
});

test('write value', function () {
    $test = new TestAbstractStorage();
    $test->setValue('new','NEWVALUE');
    expect($test->getValue('new'))->toEqual('NEWVALUE');
});

test('update value', function () {
    $test = new TestAbstractStorage();
    $test->setValue('test', 'NEWVALUE');
    expect($test->getValue('test'))->toEqual('NEWVALUE');
});

test('cache miss while reading', function () {
    Cache::flush();

    $test = new TestAbstractStorage();
    $test->setCacheID('teststorage');
    $test->getValue('test');

    expect(Cache::get('teststorage.test'))->toEqual('TESTVALUE');
});

test('cache hit while reading', function () {
    Cache::flush();

    $test = new TestAbstractStorage();
    Cache::put('teststorage.test','cachedvalue');
    $test->setCacheID('teststorage');

    expect($test->getValue('test'))->toEqual('cachedvalue');
});

test('cache update while writing', function () {
    Cache::flush();

    $test = new TestAbstractStorage();
    $test->setCacheID('teststorage');
    $test->setValue('new', 'NEWVALUE');
    expect(Cache::get('teststorage.new'))->toEqual('NEWVALUE');
});

test('cache update while updating', function () {
    Cache::flush();

    $test = new TestAbstractStorage();
    $test->setCacheID('teststorage');
    $test->getValue('test');
    expect(Cache::get('teststorage.test'))->toEqual('TESTVALUE');

    $test->setValue('test', 'NEWVALUE');
    expect(Cache::get('teststorage.test'))->toEqual('NEWVALUE');
});

test('cache outdate', function () {
    Cache::flush();

    $test = new TestAbstractStorage();
    $test->setCacheID('teststorage')->setCacheTime(1);
    // Set caching time to 1 second
    Cache::put('teststorage.test','cached',1);
    sleep(2);
    expect($test->getValue('test'))->toEqual('TESTVALUE');
});

test('array access', function () {
    $test = new TestAbstractStorage();
    expect($test->getIndexedValue('array_val',1))->toEqual('DEF');
});

test('array overwrite', function () {
    $test = new TestAbstractStorage();
    $test->setIndexedValue('array_val',1,'XYZ');
    expect($test->getIndexedValue('array_val',1))->toEqual('XYZ');
});

test('array append', function () {
    $test = new TestAbstractStorage();
    $test->setIndexedValue('array_val',null,'XYZ');
    expect($test->getIndexedValue('array_val',2))->toEqual('XYZ');
});

test('array count', function () {
    $test = new TestAbstractStorage();
    expect($test->getElementCount('array_val'))->toEqual(2);
});
