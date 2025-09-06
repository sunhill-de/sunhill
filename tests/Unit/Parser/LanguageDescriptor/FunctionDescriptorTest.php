<?php

/**
 * @file OperatorDesciptorTest.php
 * tests: /src/Parser/LanguageDescriptor/FunctionDescriptor.php
 * free of dependent units: yes
 */

use Sunhill\Parser\LanguageDescriptor\FunctionDescriptor;
use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Parser\Exceptions\LanguageDescriptorException;

uses(SunhillSimpleTestCase::class);

test('Constructor sets name', function()
{
   $test = new FunctionDescriptor('testfunc');
   expect($test->getName())->toBe('testfunc');
});

test('setReturnType() and getReturnType()', function()
{
   $test = new FunctionDescriptor('testfunc');
   $test->setReturnType('integer');
   expect($test->getReturnType())->toBe('integer');
});

test('setContext() and getContext()', function()
{
    $test = new FunctionDescriptor('testfunc');
    $test->setContext('some_context');
    expect($test->getContext())->toBe('some_context');
});

test('addMandatoryParameter()', function()
{
   $test = new FunctionDescriptor('testfunct');
   $test->addMandatoryParameter('integer');
   expect($test->getParameterCount())->toBe(1);
});

test('addOptionalParameter()', function()
{
    $test = new FunctionDescriptor('testfunct');
    $test->addOptionalParameter('integer');
    expect($test->getParameterCount())->toBe(1);
});

test('addEllipsis()', function()
{
    $test = new FunctionDescriptor('testfunct');
    $test->addEllipsis('integer');
    expect($test->getParameterCount())->toBe(1);
});

test('Function stack with no parameters', function()
{
    $test = new FunctionDescriptor('testfunct');
    $test->reset();
    expect($test->pop())->toBe(null);
});

test('Function stack with one mandatory', function()
{
    $test = new FunctionDescriptor('testfunct');
    $test->addMandatoryParameter('integer');
    $test->reset();
    expect($test->pop())->toBe('integer');
    expect($test->pop())->toBe(null);
});

test('Function stack with two mandatory', function()
{
    $test = new FunctionDescriptor('testfunct');
    $test->addMandatoryParameter('integer');
    $test->addMandatoryParameter('string');
    $test->reset();
    expect($test->pop())->toBe('integer');
    expect($test->pop())->toBe('string');
    expect($test->pop())->toBe(null);
});

test('Function stack with one mandatory and one optional', function()
{
    $test = new FunctionDescriptor('testfunct');
    $test->addMandatoryParameter('integer');
    $test->addOptionalParameter('string');
    $test->reset();
    expect($test->pop())->toBe('integer');
    expect($test->pop())->toBe('string');
    expect($test->pop())->toBe(null);
});

test('Function stack with one mandatory and ellipsis', function()
{
    $test = new FunctionDescriptor('testfunct');
    $test->addMandatoryParameter('integer');
    $test->addEllipsis('string');
    $test->reset();
    expect($test->pop())->toBe('integer');
    expect($test->pop())->toBe('string');
    expect($test->pop())->toBe('string');
});

it('Fails when adding a mandatory after an optional', function()
{
    $test = new FunctionDescriptor('testfunct');
    $test->addOptionalParameter('string');
    $test->addMandatoryParameter('integer');    
})->throws(LanguageDescriptorException::class);

it('Fails when adding an optional after an ellipsis', function()
{
    $test = new FunctionDescriptor('testfunct');
    $test->addEllipsis('integer');
    $test->addOptionalParameter('string');
})->throws(LanguageDescriptorException::class);

it('Fails when adding an mandatory after an ellipsis', function()
{
    $test = new FunctionDescriptor('testfunct');
    $test->addEllipsis('integer');
    $test->addOptionalParameter('string');
})->throws(LanguageDescriptorException::class);

