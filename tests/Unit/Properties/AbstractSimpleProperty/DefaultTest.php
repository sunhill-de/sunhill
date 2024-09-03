<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Properties\Exceptions\UninitializedValueException;
use Sunhill\Properties\Tests\TestSupport\Properties\NonAbstractSimpleProperty;
use Sunhill\Properties\Tests\TestSupport\Storages\TestAbstractIDStorage;
test('default', function () {
    $test = new NonAbstractSimpleProperty();
    $test->default(5);

    expect($test->getDefault())->toEqual(5);
});
test('nullable', function () {
    $test = new NonAbstractSimpleProperty();
    $test->nullable();

    expect($test->getNullable())->toBeTrue();
});
test('get value with default', function () {
    $test = new NonAbstractSimpleProperty();
    $test->default(5);
    $storage = new TestAbstractIDStorage();

    $test->setStorage($storage);

    expect($test->getValue())->toEqual(5);
});
test('get value with default null', function () {
    $test = new NonAbstractSimpleProperty();
    $test->default(null);
    $storage = new TestAbstractIDStorage();

    $test->setStorage($storage);

    expect($test->getValue())->toBeNull();
});
test('get value without default', function () {
    $this->expectException(UninitializedValueException::class);

    $test = new NonAbstractSimpleProperty();
    $storage = new TestAbstractIDStorage();

    $test->setStorage($storage);

    $test->getValue();
});