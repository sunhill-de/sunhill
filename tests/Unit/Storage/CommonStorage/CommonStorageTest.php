<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Storage\Exceptions\FieldNotAvaiableException;
use Sunhill\Storage\Exceptions\FieldNotAnArrayException;
use Sunhill\Tests\TestSupport\Storages\DummyCommonStorage;
use Sunhill\Properties\Exceptions\InvalidIndexException;

uses(SimpleTestCase::class);

test('getValue works', function()
{
    $test = new DummyCommonStorage();
    expect($test->getValue('str_value'))->toBe('ABC');    
});

it('Fails when field is not avaiable', function()
{
    $test = new DummyCommonStorage();
    $test->getValue('dontexist');
})->throws(FieldNotAvaiableException::class);

test('getIndexedValue works', function()
{
    $test = new DummyCommonStorage();
    expect($test->getIndexedValue('array_value',1))->toBe(22);
});

it('fails running getIndexedValue when index does not exist', function()
{
    $test = new DummyCommonStorage();
    $test->getIndexedValue('array_value',999);
})->throws(InvalidIndexException::class);

it('Fails when array field is not avaiable', function()
{
    $test = new DummyCommonStorage();
    $test->getIndexedValue('dontexist',1);
    
})->throws(FieldNotAvaiableException::class);

it('Fails when array field is not an array', function()
{
    $test = new DummyCommonStorage();
    $test->getIndexedValue('str_value',1);    
})->throws(FieldNotAnArrayException::class);

test('getElementCount works', function()
{
    $test = new DummyCommonStorage();
    expect($test->getElementCount('array_value'))->toBe(3);
});

it('fails running getElementCount() when array field is not avaiable', function()
{
    $test = new DummyCommonStorage();
    $test->getElementCount('dontexist');
    
})->throws(FieldNotAvaiableException::class);

it('fails running getElementCount() when array field is not an array', function()
{
    $test = new DummyCommonStorage();
    $test->getElementCount('str_value');
})->throws(FieldNotAnArrayException::class);

it('fails running getOffsetExists() when array field does not exist', function()
{
    $test = new DummyCommonStorage();
    $test->getOffsetExists('dontexists',1);
})->throws(FieldNotAvaiableException::class);

test('getOffsetExists() works', function()
{
    $test = new DummyCommonStorage();
    expect($test->getOffsetExists('array_value',1))->toBe(true);
    expect($test->getOffsetExists('array_value',99))->toBe(false);
});


it('fails running getOffsetExists() when array field is not an array', function()
{
    $test = new DummyCommonStorage();
    $test->getOffsetExists('str_value',1);
})->throws(FieldNotAnArrayException::class);
