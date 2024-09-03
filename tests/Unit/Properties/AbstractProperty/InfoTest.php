<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Properties\Exceptions\PropertyException;
use Sunhill\Properties\Properties\Exceptions\PropertyKeyDoesntExistException;
use Sunhill\Properties\Tests\TestSupport\Properties\NonAbstractProperty;
test('unknown method', function () {
    $this->expectException(PropertyException::class);

    $test = new NonAbstractProperty();
    $test->unknownMethod();
});
test('get info', function () {
    expect(NonAbstractProperty::getInfo('name'))->toEqual('NonAbstractProperty');
});
test('get nonexisting info', function () {
    $this->expectException(PropertyKeyDoesntExistException::class);
    NonAbstractProperty::getInfo('nonexisting');
});
test('get nonexisting info with default', function () {
    expect(NonAbstractProperty::getInfo('nonexisting','default'))->toEqual('default');
});
test('translate get info', function () {
    expect(NonAbstractProperty::getInfo('description'))->toEqual('trans:A base test class for an abstract property.');
});
test('has key', function () {
    expect(NonAbstractProperty::hasInfo('userkey'))->toBeTrue();
    expect(NonAbstractProperty::hasInfo('nonexisting'))->toBeFalse();
});
test('get all keys', function () {
    $info = NonAbstractProperty::getAllInfos();
    expect(isset($info['userkey']))->toBeTrue();
});