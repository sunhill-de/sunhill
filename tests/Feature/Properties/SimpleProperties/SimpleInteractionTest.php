<?php

use Sunhill\Tests\TestSupport\Storages\DummyStorage;
use Sunhill\Types\TypeFloat;
use Sunhill\Properties\Exceptions\UninitializedValueException;
use Sunhill\Properties\ArrayProperty;
use Sunhill\Properties\Exceptions\InvalidIndexException;
use Sunhill\Properties\Exceptions\InvalidValueException;
use Sunhill\Tests\SimpleTestCase;

uses(SimpleTestCase::class);

test('read initialized value', function()
{
    $storage = new DummyStorage();
    $test = new TypeFloat();
    $test->setName('keyB')->setStorage($storage);
    
    expect($test->getValue())->toBe(3.56);
});

it('fails when reading uninitialized value',function()
{
    $storage = new DummyStorage();
    $test = new TypeFloat();
    $test->setName('unknown')->setStorage($storage);
    
    $dummy = $test->getValue();
})->throws(UninitializedValueException::class);

test('read uninitialized value with default', function()
{
    $storage = new DummyStorage();
    $test = new TypeFloat();
    $test->setName('unknown')->setStorage($storage)->setDefault(1.23);
    
    expect($test->getValue())->toBe(1.23);
});

test('read array value', function()
{
    $storage = new DummyStorage();
    $test = new ArrayProperty();
    $test->setName('keyC')->setStorage($storage);
    
    expect($test->offsetGet(1))->toBe(2);
});

it('fails when reading an uninitialized array value', function()
{
    $storage = new DummyStorage();
    $test = new ArrayProperty();
    $test->setName('unknown')->setStorage($storage);
    
    expect($test->offsetGet(1))->toBe(2);    
})->throws(UninitializedValueException::class);

it('fails when reading an uninitialized array index', function()
{
    $storage = new DummyStorage();
    $test = new ArrayProperty();
    $test->setName('keyC')->setStorage($storage);
    
    $test->offsetGet(999);    
})->throws(InvalidIndexException::class);

test('Writing a new value', function() {
    $storage = new DummyStorage();
    $test = new TypeFloat();
    $test->setName('keyC')->setStorage($storage);
    
    $test->setValue(1.34);
    expect($test->getValue())->toBe(1.34);    
});

it('Fails when writing a value with wrong type', function() {
        $storage = new DummyStorage();
        $test = new TypeFloat();
        $test->setName('keyC')->setStorage($storage);
        
        $test->setValue('ABC');
})->throws(InvalidValueException::class);
        