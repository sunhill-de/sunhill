<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Parser\Helpers\FunctionDescriptor;

uses(SunhillTestCase::class);

test('function with no parameters', function()
{
    $test = new FunctionDescriptor('testFunction');
    $test->setReturnType('integer');
    
    expect($test->getName())->toBe('testFunction');
    expect($test->getReturnType())->toBe('integer');
    expect($test->getTotalParameterCount())->toBe(0);
    expect($test->getMandatoryParameterCount())->toBe(0);
});

test('function with one mandatory parameters', function()
{
    $test = new FunctionDescriptor('testFunction');
    $test->setReturnType('integer');
    $test->addParameter('integer');

    expect($test->getTotalParameterCount())->toBe(1);
    expect($test->getMandatoryParameterCount())->toBe(1);    
});

test('function with one optional parameters', function()
{
    $test = new FunctionDescriptor('testFunction');
    $test->setReturnType('integer');
    $test->addParameter('integer', true);
    
    expect($test->getTotalParameterCount())->toBe(1);
    expect($test->getMandatoryParameterCount())->toBe(0);
});

test('function with one mandatory and one optional parameters', function()
{
    $test = new FunctionDescriptor('testFunction');
    $test->setReturnType('integer');
    $test->addParameter('integer');
    $test->addParameter('string', true);
    
    expect($test->getTotalParameterCount())->toBe(2);
    expect($test->getMandatoryParameterCount())->toBe(1);
});

test('function with unlimited parameters', function()
{
    $test = new FunctionDescriptor('testFunction');
    $test->setReturnType('integer')->setUnlimitedParameters(2);
    
    expect($test->getUnlimitedParameters())->toBe(true);
    expect($test->getMinimumParameterCount())->toBe(2);
    expect($test->getTotalParameterCount())->toBe(-1);
    expect($test->getMandatoryParameterCount())->toBe(0);
});

test('function with one mandatory and unlimited optional parameters', function()
{
    $test = new FunctionDescriptor('testFunction');
    $test->setReturnType('integer')->setUnlimitedParameters(2);
    $test->addParameter('integer');
    
    expect($test->getTotalParameterCount())->toBe(-1);
    expect($test->getMandatoryParameterCount())->toBe(1);
});



