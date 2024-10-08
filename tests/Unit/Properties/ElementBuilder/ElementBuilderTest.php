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

uses(\Sunhill\Tests\TestCase::class);

test('Property is called from ElementBuilder', function()
{
   $property = Mockery::mock(RecordProperty::class);
   $property->shouldReceive('appendElement')->once();
   
   $test = new ElementBuilder($property);
   $test->addProperty(NonAbstractProperty::class, 'name');
});

test('LookupProperty with property works', function()
{
   $property = Mockery::mock(RecordProperty::class);
   $child_property = new NonAbstractProperty();
   
   $builder = new ElementBuilder($property);
   $test = $builder->lookUpProperty($child_property);
   expect($test)->toBe($child_property);
});

test('LookupProperty with namespaced class', function()
{
    $property = Mockery::mock(RecordProperty::class);
    
    $builder = new ElementBuilder($property);
    $test = $builder->lookUpProperty(NonAbstractProperty::class);
    expect($test::class)->toBe(NonAbstractProperty::class);
});

test('LookupProperty with named class', function()
{
    $property = Mockery::mock(RecordProperty::class);
    Properties::shouldReceive('getNamespaceOfClass')->with('test')->andReturn(NonAbstractProperty::class);

    $builder = new ElementBuilder($property);
    $test = $builder->lookUpProperty('test');
    expect($test::class)->toBe(NonAbstractProperty::class);
});

it('fails when LookupProperty is called with a non-property object', function()
{
    $property = Mockery::mock(RecordProperty::class);
    
    $builder = new ElementBuilder($property);
    $subproperty = new \stdClass();
    $builder->lookUpProperty($subproperty);
})->throws(TypeError::class);

it('fails when LookupProperty is called with an unknown property name', function()
{
    $property = Mockery::mock(RecordProperty::class);
    Properties::shouldReceive('getNamespaceOfClass')->with(5)->andReturn(null);
    
    $builder = new ElementBuilder($property);
    $builder->lookUpProperty(5);
})->throws(NotAPropertyException::class);

