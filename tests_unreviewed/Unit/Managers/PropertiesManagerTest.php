<?php

uses(\Sunhill\Tests\TestCase::class);
use Sunhill\Managers\PropertiesManager;

use Sunhill\Managers\Exceptions\PropertyClassDoesntExistException;
use Sunhill\Managers\Exceptions\GivenClassNotAPropertyException;
use Sunhill\Managers\Exceptions\PropertyNotRegisteredException;
use Sunhill\Managers\Exceptions\PropertyNameAlreadyRegisteredException;
use Sunhill\Managers\Exceptions\UnitNameAlreadyRegisteredException;
use Sunhill\Managers\Exceptions\UnitNotRegisteredException;
use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;
use Sunhill\Tests\Unit\Managers\Samples\First;
use Sunhill\Tests\Unit\Managers\Samples\Second;
use Sunhill\Tests\Unit\Managers\Samples\Third;
use Sunhill\Storage\AbstractStorage;

test('register property', function () {
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);

    expect(isset(getProtectedProperty($test, 'registered_properties')['NonAbstractProperty']))->toBeTrue();
});

it('fails when registering double property', function () {
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);
    $test->registerProperty(NonAbstractProperty::class);
})->throws(PropertyNameAlreadyRegisteredException::class);

test('register double property with alias', function () {
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);
    $test->registerProperty(NonAbstractProperty::class,'alias');

    expect(isset(getProtectedProperty($test, 'registered_properties')['NonAbstractProperty']))->toBeTrue();
    expect(isset(getProtectedProperty($test, 'registered_properties')['alias']))->toBeTrue();
});

it('fails when registering property with non accessible class', function () {
    $test = new PropertiesManager();

    $test->registerProperty('something');
})->throws(PropertyClassDoesntExistException::class);

it('fails when registering property with no property class', function () {
    $test = new PropertiesManager();

    $test->registerProperty(\StdClass::class);
})->throws(GivenClassNotAPropertyException::class);

test('property registred', function () {
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);

    expect($test->isPropertyRegistered('NonAbstractProperty'))->toBeTrue();
    expect($test->isPropertyRegistered(NonAbstractProperty::class))->toBeTrue();
    expect($test->isPropertyRegistered(new NonAbstractProperty()))->toBeTrue();
    expect($test->isPropertyRegistered('nonexisting'))->toBeFalse();
});

test('get namespace of property pass', function () {
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);

    expect($test->getNamespaceOfProperty('NonAbstractProperty'))->toEqual(NonAbstractProperty::class);
    expect($test->getNamespaceOfProperty(NonAbstractProperty::class))->toEqual(NonAbstractProperty::class);
    expect($test->getNamespaceOfProperty(new NonAbstractProperty()))->toEqual(NonAbstractProperty::class);
});

it('fails when geting namespace of non registrerd property', function () {
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);

    $test->getNamespaceOfProperty('nonexisting');
})->throws(PropertyNotRegisteredException::class);

test('get name of property pass', function () {
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);

    expect($test->getNameOfProperty('NonAbstractProperty'))->toEqual('NonAbstractProperty');
    expect($test->getNameOfProperty(NonAbstractProperty::class))->toEqual('NonAbstractProperty');
    expect($test->getNameOfProperty(new NonAbstractProperty()))->toEqual('NonAbstractProperty');
});

it('fails when geting name of nonregistered property', function () {
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);

    $test->getNameOfProperty('nonexisting');
})->throws(PropertyNotRegisteredException::class);

test('property has method pass', function()
{
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);
    
    expect($test->propertyHasMethod('NonAbstractProperty','setName'))->toBe(true);
});

test('property has method fail', function()
{
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);
    
    expect($test->propertyHasMethod('NonAbstractProperty','nonexistantmethod'))->toBe(false);
});

test('createProperty() works', function()  
{
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);
    
    $help = $test->createProperty('NonAbstractProperty');
    expect(is_a($help,NonAbstractProperty::class))->toBe(true);
});

test('register unit', function () {
    $test = new PropertiesManager();
    $test->registerUnit('test_name','test_unit', 'test_group', 'test_basic');

    expect(isset(getProtectedProperty($test, 'registered_units')['test_name']))->toBeTrue();
});

it('if fails when registering double unit', function () {
    $test = new PropertiesManager();
    $test->registerUnit('test_name','test_unit', 'test_group', 'test_basic');
    $test->registerUnit('test_name','test_unit', 'test_group', 'test_basic');
})->throws(UnitNameAlreadyRegisteredException::class);

test('unit registred', function () {
    $test = new PropertiesManager();
    $test->registerUnit('test_name','test_unit', 'test_group', 'test_basic');

    expect($test->isUnitRegistered('test_name'))->toBeTrue();
    expect($test->isUnitRegistered('unknown'))->toBeFalse();
});

test('get unit', function () {
    $test = new PropertiesManager();
    $test->registerUnit('test_name','test_unit', 'test_group', 'test_basic');

    expect($test->getUnit('test_name'))->toEqual('test_unit');
});

it('fails when geting unregistered unit', function () {

    $test = new PropertiesManager();
    $test->registerUnit('test_name','test_unit', 'test_group', 'test_basic');

    $test->getUnit('unknown');
})->throws(UnitNotRegisteredException::class);

test('get unit group', function () {
    $test = new PropertiesManager();
    $test->registerUnit('test_name','test_unit', 'test_group', 'test_basic');

    expect($test->getUnitGroup('test_name'))->toEqual('test_group');
});

test('get unit basic', function () {
    $test = new PropertiesManager();
    $test->registerUnit('test_name','test_unit', 'test_group', 'test_basic');

    expect($test->getUnitBasic('test_name'))->toEqual('test_basic');
});

test('calculate to basic', function () {
    $test = new PropertiesManager();
    $test->registerUnit('test_basic','test_basicunit', 'test_group');
    $test->registerUnit('test_name','test_unit', 'test_group', 'test_basic',
        function($input) { return $input * 2; },
        function($input) { return $input / 2; } );
    expect($test->calculateToBasic('test_name', 2))->toEqual(4);
});

test('calculate from basic', function () {
    $test = new PropertiesManager();
    $test->registerUnit('test_basic','test_basicunit', 'test_group');
    $test->registerUnit('test_name','test_unit', 'test_group', 'test_basic',
        function($input) { return $input * 2; },
        function($input) { return $input / 2; } );
    expect($test->calculateFromBasic('test_name', 4))->toEqual(2);
});

