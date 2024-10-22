<?php

use Sunhill\Tests\TestSupport\Storages\DummyStorage;
use Sunhill\Types\TypeFloat;
use Sunhill\Properties\Exceptions\UninitializedValueException;
use Sunhill\Properties\ArrayProperty;
use Sunhill\Properties\Exceptions\InvalidIndexException;
use Sunhill\Properties\Exceptions\InvalidValueException;
use Sunhill\Tests\SimpleTestCase;
use Sunhill\Facades\Properties;
use Sunhill\Tests\SunhillTestCase;

uses(SunhillTestCase::class);

test('read initialized value', function()
{
    $test = Properties::createProperty('float', 'keyB', DummyStorage::class);
    
    expect($test->getValue())->toBe(3.56);
});

it('fails when reading uninitialized value',function()
{
    $test = Properties::createProperty('float', 'unknown', DummyStorage::class);
        
    $test->getValue();
})->throws(UninitializedValueException::class);

test('read uninitialized value with default', function()
{
    $test = Properties::createProperty('float', 'unknown', DummyStorage::class);
    $test->setDefault(1.23);
    
    expect($test->getValue())->toBe(1.23);
});

test('read array value', function()
{
    $test = Properties::createProperty('array', 'keyC', DummyStorage::class);
        
    expect($test->offsetGet(1))->toBe(2);
});

it('fails when reading an uninitialized array value', function()
{
    $test = Properties::createProperty('array', 'unknown', DummyStorage::class);

    expect($test->offsetGet(1))->toBe(2);    
})->throws(UninitializedValueException::class);

it('fails when reading an uninitialized array index', function()
{
    $test = Properties::createProperty('array', 'keyC', DummyStorage::class);
    
    $test->offsetGet(999);    
})->throws(InvalidIndexException::class);

test('Writing a new value', function() {
    $test = Properties::createProperty('float', 'keyC', DummyStorage::class);

    $test->setValue(1.34);
    expect($test->getValue())->toBe(1.34);    
});

it('Fails when writing a value with wrong type', function() {
    $test = Properties::createProperty('float', 'keyC', DummyStorage::class);
    
        $test->setValue('ABC');
})->throws(InvalidValueException::class);
        