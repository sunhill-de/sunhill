<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Parser\LanguageDescriptor\FunctionDescriptor;

uses(SunhillTestCase::class);

test('FunctionDescriptor with no parameters', function()
{
    $test = new FunctionDescriptor('any:testfunct():integer');
    expect($test->getName())->toBe('testfunct');
    expect($test->getReturnType())->toBe('integer');
    expect($test->getParameterCount())->toBe(0);
    expect($test->getContext())->toBe('any');
    $test->reset();
    expect($test->pop())->toBe(null);
});

test('FunctionDescriptor with one mandatory parameter', function()
{
    $test = new FunctionDescriptor('testfunct(integer):integer');
    $test->reset();
    expect($test->pop())->toBe('!integer');
    expect($test->pop())->toBe(null);
});

test('FunctionDescriptor with two mandatory parameters', function()
{
    $test = new FunctionDescriptor('testfunct(integer,string):integer');
    $test->reset();
    expect($test->pop())->toBe('!integer');
    expect($test->pop())->toBe('!string');
    expect($test->pop())->toBe(null);
});

test('FunctionDescriptor with one mandatory parameter and one optional', function()
{
    $test = new FunctionDescriptor('testfunct(integer,?string):integer');
    $test->reset();
    expect($test->pop())->toBe('!integer');
    expect($test->pop())->toBe('?string');
    expect($test->pop())->toBe(null);
});

test('FunctionDescriptor with two optional parameters', function()
{
    $test = new FunctionDescriptor('testfunct(?integer,?string):integer');
    $test->reset();
    expect($test->pop())->toBe('?integer');
    expect($test->pop())->toBe('?string');
    expect($test->pop())->toBe(null);
});

test('FunctionDescriptor with ellipsis parameters', function()
{
    $test = new FunctionDescriptor('testfunct(...integer):integer');
    $test->reset();
    expect($test->pop())->toBe('?integer');
    expect($test->pop())->toBe('?integer');
});

test('FunctionDescriptor with one mandatory annd ellipsis parameters', function()
{
    $test = new FunctionDescriptor('testfunct(integer,...integer):integer');
    $test->reset();
    expect($test->pop())->toBe('!integer');
    expect($test->pop())->toBe('?integer');
    expect($test->pop())->toBe('?integer');
});

