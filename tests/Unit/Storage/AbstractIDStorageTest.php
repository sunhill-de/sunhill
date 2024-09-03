<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Tests\TestSupport\Storages\TestAbstractIDStorage;


test('load from storage', function () {
    $test = new TestAbstractIDStorage();
    $test->setID(1);
    expect($test->getValue('test_str'))->toEqual('DEF');
    expect($test->isDirty())->toBeFalse();
});

test('store new entry', function () {
    $test = new TestAbstractIDStorage();
    $test->setValue('test_str','AAA');
    $test->setValue('test_int',111);
    $test->commit();
    expect($test->getID())->toEqual(2);
    expect($test->data[2]['test_str'])->toEqual('AAA');
});

test('update entry', function () {
    $test = new TestAbstractIDStorage();
    $test->setID(1);
    $test->setValue('test_str','AAA');
    $test->setValue('test_int',111);
    $test->commit();
    expect($test->getID())->toEqual(1);
    expect($test->data[1]['test_str'])->toEqual('AAA');
});
