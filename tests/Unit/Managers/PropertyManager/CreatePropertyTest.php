<?php

/**
 * @file CreatePropertyTest.php
 * tests: /src/Managers/PropertiesManagaer.php
 * free of dependent units: yes
 */

use Sunhill\Facades\Properties;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Storage\CallbackStorage;
use Sunhill\Tests\SunhillLaravelTestCase;
use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;
use Sunhill\Tests\TestSupport\Storages\DummyStorage;

uses(SunhillLaravelTestCase::class);

test('createProperty() with an instance', function () {
    Properties::registerProperty(NonAbstractProperty::class, 'testProperty');
    $result = new NonAbstractProperty;
    $property = Properties::createProperty($result, 'test');

    expect($property)->toBe($result);
    expect($property->getName())->toBe('test');
});

test('createProperty() with an fully qualified class name', function () {
    Properties::registerProperty(NonAbstractProperty::class, 'testProperty');
    $property = Properties::createProperty(NonAbstractProperty::class);

    expect(is_a($property, NonAbstractProperty::class))->toBe(true);
});

test('createProperty() with a property name', function () {
    Properties::registerProperty(NonAbstractProperty::class, 'testProperty');
    $property = Properties::createProperty('testProperty', 'test');

    expect(is_a($property, NonAbstractProperty::class))->toBe(true);
});

test('createProperty() without a name', function () {
    Properties::registerProperty(NonAbstractProperty::class, 'testProperty');
    $property = Properties::createProperty('testProperty');

    expect($property->getName())->toBe('test_int');
});

test('createProperty() with storage object', function () {
    $storage = \Mockery::mock(AbstractStorage::class);
    Properties::registerProperty(NonAbstractProperty::class, 'testProperty');
    $property = Properties::createProperty('testProperty', 'test', $storage);

    expect($property->getStorage())->toBe($storage);

});

test('createProperty() with class name', function () {
    Properties::registerProperty(NonAbstractProperty::class, 'testProperty');
    $property = Properties::createProperty('testProperty', 'test', DummyStorage::class);

    expect(is_a($property->getStorage(), DummyStorage::class))->toBe(true);

});

test('createProperty() with callback', function () {
    Properties::registerProperty(NonAbstractProperty::class, 'testProperty');
    $property = Properties::createProperty('testProperty', 'test', function () {
        return ['keyA' => 'valueA'];
    });

    expect(is_a($property->getStorage(), CallbackStorage::class))->toBe(true);

});
