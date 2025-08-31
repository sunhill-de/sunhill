<?php

/**
 * @file InfoTest.php
 * tests: /src/Properties/AbstractProperty.php
 * free of dependent units: yes
 */
use Sunhill\Properties\AbstractProperty;
use Sunhill\Properties\Exceptions\PropertyException;
use Sunhill\Properties\Exceptions\PropertyKeyDoesntExistException;
use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;

uses(SunhillSimpleTestCase::class);

test('unknown method', function () {
    $this->expectException(PropertyException::class);

    $test = new NonAbstractProperty;
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
    expect(NonAbstractProperty::getInfo('nonexisting', 'default'))->toEqual('default');
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

test('default setupInfos()', function () {
    expect(AbstractProperty::getInfo('name'))->toBe('AbstractProperty');
});
