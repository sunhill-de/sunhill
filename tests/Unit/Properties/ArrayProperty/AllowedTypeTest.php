<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Properties\ArrayProperty;
use Sunhill\Types\TypeInteger;
use Sunhill\Facades\Properties;
use Sunhill\Properties\Exceptions\InvalidParameterException;

uses(SunhillTestCase::class);

test('set allowed element with a class works', function()
{
    $help = new TypeInteger();
    
    $test = new ArrayProperty();
    $test->setAllowedElementType($help);
    
    expect($test->getAllowedElementType())->toBe(TypeInteger::class);
});

test('set allowed element with a fqcn works', function()
{
    $test = new ArrayProperty();
    $test->setAllowedElementType(TypeInteger::class);
    
    expect($test->getAllowedElementType())->toBe(TypeInteger::class);    
});

test('set allowed element with class alias works', function()
{
    Properties::shouldReceive('isPropertyRegistered')->with('integer')->andReturn(true);
    Properties::shouldReceive('getNamespaceOfProperty')->with('integer')->andReturn(TypeInteger::class);
    
    $test = new ArrayProperty();
    $test->setAllowedElementType('integer');
    
    expect($test->getAllowedElementType())->toBe(TypeInteger::class);
});

it('fails when a non existent class alias works is assigned', function()
{
    Properties::shouldReceive('isPropertyRegistered')->with('integer')->andReturn(false);
    
    $test = new ArrayProperty();
    $test->setAllowedElementType('integer');
    
})->throws(InvalidParameterException::class);

it('fails when something else is assigned', function()
{
    $test = new ArrayProperty();
    $test->setAllowedElementType(3);
    
})->throws(InvalidParameterException::class);

it('fails when an array is passed as element type', function()
{
    $test = new ArrayProperty();
    $test->setAllowedElementType(ArrayProperty::class);    
})->throws(InvalidParameterException::class);

test('valid test works', function()
{
    $test = new ArrayProperty();
    $test->setAllowedElementType(TypeInteger::class);
    
    expect($test->checkElement(12))->toBe(true);
    expect($test->checkElement('A'))->toBe(false);
});
