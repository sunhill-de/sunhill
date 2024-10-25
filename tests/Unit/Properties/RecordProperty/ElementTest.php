<?php

use Sunhill\Properties\RecordProperty;
use Sunhill\Tests\SunhillTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;
use Sunhill\Properties\Exceptions\NotAPropertyException;
use Sunhill\Properties\Exceptions\PropertyNameAlreadyGivenException;
use Sunhill\Properties\Exceptions\PropertyHasNoNameException;
use Sunhill\Properties\Exceptions\PropertyAlreadyInListException;

uses(SunhillTestCase::class);

test('appendElement() with only an element object', function()
{
    $test = new RecordProperty();
    $element = new NonAbstractProperty();
    expect($test->appendElement($element))->toBe($element);
});

test('appendElement() with only a fully qualified class name', function()
{
    $test = new RecordProperty();
    expect(is_a($test->appendElement(NonAbstractProperty::class),NonAbstractProperty::class))->toBe(true);
});

test('appendElement() with only a property name', function()
{
    Properties::shouldReceive('getNamespaceOfProperty')->once()->with('test')->andReturn(NonAbstractProperty::class);
    $test = new RecordProperty();
    expect(is_a($test->appendElement('test'),NonAbstractProperty::class))->toBe(true);
});

it('fails when passing a non property', function()
{
    $test = new RecordProperty();
    $element = new stdClass();
    $test->appendElement($element);    
})->throws(NotAPropertyException::class);

test('Passing a name works', function() 
{
    $test = new RecordProperty();
    $element = new NonAbstractProperty();
    expect($test->appendElement($element,'test')->getName())->toBe('test');
    
});

it('Fails when property name was already given', function()
{
    $test = new RecordProperty();
    $element1 = new NonAbstractProperty();
    $element2 = new NonAbstractProperty();
    $test->appendElement($element1,'test');
    $test->appendElement($element2,'test');
})->throws(PropertyNameAlreadyGivenException::class);
    
it('Fails when property was already appended', function()
{
    $test = new RecordProperty();
    $element1 = new NonAbstractProperty();
    $test->appendElement($element1,'test1');
    $test->appendElement($element1,'test2');
})->throws(PropertyAlreadyInListException::class);


it('Fails when property has no name', function()
{
    $test = new RecordProperty();
    $element1 = new NonAbstractProperty();
    setProtectedProperty($element1, '_name', null);
    $test->appendElement($element1);
})->throws(PropertyHasNoNameException::class);


    