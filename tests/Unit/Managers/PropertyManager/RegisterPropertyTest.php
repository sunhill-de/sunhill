<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Managers\PropertiesManager;
use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;
use Sunhill\Managers\Exceptions\PropertyNameAlreadyRegisteredException;
use Sunhill\Managers\Exceptions\PropertyClassDoesntExistException;
use Sunhill\Managers\Exceptions\GivenClassNotAPropertyException;
use Sunhill\Managers\Exceptions\PropertyNotRegisteredException;

uses(SimpleTestCase::class);

test('register property', function () 
{
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);
    
    expect(isset(getProtectedProperty($test, 'registered_properties')['NonAbstractProperty']))->toBeTrue();
});
    
it('fails when registering double property', function ()
{
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);
    $test->registerProperty(NonAbstractProperty::class);
})->throws(PropertyNameAlreadyRegisteredException::class);

test('register double property with alias', function ()
{
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);
    $test->registerProperty(NonAbstractProperty::class,'alias');
    
    expect(isset(getProtectedProperty($test, 'registered_properties')['NonAbstractProperty']))->toBeTrue();
    expect(isset(getProtectedProperty($test, 'registered_properties')['alias']))->toBeTrue();
});

it('fails when registering property with non accessible class', function ()
{
    $test = new PropertiesManager();
    
    $test->registerProperty('something');
})->throws(PropertyClassDoesntExistException::class);

it('fails when registering property with no property class', function ()
{
    $test = new PropertiesManager();
    
    $test->registerProperty(\StdClass::class);
})->throws(GivenClassNotAPropertyException::class);

test('property registred', function ()
{
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);
    
    expect($test->isPropertyRegistered('NonAbstractProperty'))->toBeTrue();
    expect($test->isPropertyRegistered(NonAbstractProperty::class))->toBeTrue();
    expect($test->isPropertyRegistered(new NonAbstractProperty()))->toBeTrue();
    expect($test->isPropertyRegistered('nonexisting'))->toBeFalse();
});

test('get namespace of property pass', function ()
{
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);
    
    expect($test->getNamespaceOfProperty('NonAbstractProperty'))->toEqual(NonAbstractProperty::class);
    expect($test->getNamespaceOfProperty(NonAbstractProperty::class))->toEqual(NonAbstractProperty::class);
    expect($test->getNamespaceOfProperty(new NonAbstractProperty()))->toEqual(NonAbstractProperty::class);
});

it('fails when geting namespace of non registrerd property', function ()
{
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);
    
    $test->getNamespaceOfProperty('nonexisting');
})->throws(PropertyNotRegisteredException::class);

test('get name of property pass', function ()
{
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);
    
    expect($test->getNameOfProperty('NonAbstractProperty'))->toEqual('NonAbstractProperty');
    expect($test->getNameOfProperty(NonAbstractProperty::class))->toEqual('NonAbstractProperty');
    expect($test->getNameOfProperty(new NonAbstractProperty()))->toEqual('NonAbstractProperty');
});

it('fails when geting name of nonregistered property', function ()
{
    $test = new PropertiesManager();
    $test->registerProperty(NonAbstractProperty::class);
    
    $test->getNameOfProperty('nonexisting');
})->throws(PropertyNotRegisteredException::class);

                    