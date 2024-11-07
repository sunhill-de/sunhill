<?php

/**
Tests src/Properties/ElementBuilder.php
 */
use Sunhill\Properties\ElementBuilder;
use Sunhill\Properties\Exceptions\PropertyNotSetException;
use Sunhill\Properties\RecordProperty;
use Sunhill\Properties\AbstractProperty;
use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;
use Sunhill\Facades\Properties;
use Sunhill\Properties\Exceptions\NotAPropertyException;
use Sunhill\Properties\Exceptions\PropertyHasNoNameException;

uses(\Sunhill\Tests\TestCase::class);

test('addProperty() and getElememts() work', function()
{
   $test = new ElementBuilder();
   $test->addProperty(NonAbstractProperty::class, 'name');
   
   $elements = $test->getElements();
   expect(count($elements))->toBe(1);
   expect(is_a($elements['name'], NonAbstractProperty::class))->toBe(true);
});

test('LookupProperty with property works', function()
{
   $child_property = new NonAbstractProperty();
   
   $builder = new ElementBuilder();
   $test = $builder->lookUpProperty($child_property);
   expect($test)->toBe($child_property);
});

test('LookupProperty with namespaced class', function()
{
    $builder = new ElementBuilder();
    $test = $builder->lookUpProperty(NonAbstractProperty::class);
    expect($test::class)->toBe(NonAbstractProperty::class);
});

test('LookupProperty with named class', function()
{
    Properties::shouldReceive('getNamespaceOfProperty')->with('test')->andReturn(NonAbstractProperty::class);

    $builder = new ElementBuilder();
    $test = $builder->lookUpProperty('test');
    expect($test::class)->toBe(NonAbstractProperty::class);
});

it('fails when LookupProperty is called with a non-property object', function()
{
    $builder = new ElementBuilder();
    $subproperty = new \stdClass();
    $builder->lookUpProperty($subproperty);
})->throws(TypeError::class);

it('fails when LookupProperty is called with an unknown property name', function()
{
    Properties::shouldReceive('getNamespaceOfProperty')->with('unknown')->andReturn(null);
    
    $builder = new ElementBuilder();
    $builder->lookUpProperty('unknown');
})->throws(NotAPropertyException::class);

test('Lookup with pseudo method', function()
{
    Properties::shouldReceive('getNamespaceOfProperty')->with('test')->andReturn(NonAbstractProperty::class);
    $builder = new ElementBuilder();
    
    $builder->test('test_param');
    
    expect(is_a($builder->getElements()['test_param'],NonAbstractProperty::class))->toBe(true);
});

it('Fails when name is missing with pseudo method', function()
{
    Properties::shouldReceive('getNamespaceOfProperty')->with('test')->andReturn(NonAbstractProperty::class);
    $builder = new ElementBuilder();
    
    $builder->test();    
})->throws(PropertyHasNoNameException::class);
