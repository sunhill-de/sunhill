<?php

use Sunhill\Properties\ElementBuilder;
use Sunhill\Properties\Exceptions\PropertyNotSetException;
use Sunhill\Properties\RecordProperty;
use Sunhill\Properties\AbstractProperty;
use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;
use Sunhill\Facades\Properties;

uses(\Sunhill\Tests\TestCase::class);

test('Property is called from ElementBuilder', function()
{
   $property = Mockery::mock(RecordProperty::class);
   $property->shouldReceive('appendElement')->once();
   
   $test = new ElementBuilder($property);
   $test->addProperty(AbstractProperty::class, 'name');
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

