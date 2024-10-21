<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Storages\DummyPersistentPoolStorage;
use Sunhill\Storage\Exceptions\InvalidIDException;
use Sunhill\Storage\Exceptions\IDNotFoundException;
use Sunhill\Storage\Exceptions\StorageAlreadyLoadedException;

uses(SimpleTestCase::class);

test("isInitialized() works when nothing loaded", function()
{
    $test = new DummyPersistentPoolStorage();
    expect($test->getIsInitialized('str_field'))->toBe(false);
});

test("load() works with isLoaded()", function()
{
    $test = new DummyPersistentPoolStorage();
    expect($test->isLoaded())->toBe(false);
    $test->load(1);
    expect($test->isLoaded())->toBe(true);
});

test("load() does its job", function()
{
    $test = new DummyPersistentPoolStorage();
    $test->load(1);
    expect($test->getValue('str_field'))->toBe('DEF');
});

test('load() does its jobs with arrays', function()
{
    $test = new DummyPersistentPoolStorage();
    $test->load(1);
    expect($test->getIndexedValue('array_field',1))->toBe(5);
    
});

test("isInitialized() works when something loaded", function()
{
    $test = new DummyPersistentPoolStorage();
    $test->load(1);
    expect($test->getIsInitialized('str_field'))->toBe(true);
});


it('fails when load() is called with a invalid id', function()
{
    $test = new DummyPersistentPoolStorage();
    $test->load('ABC');    
})->throws(InvalidIDException::class);

it('fails when load() is called with a non existing id', function()
{
    $test = new DummyPersistentPoolStorage();
    $test->load(999);
})->throws(IDNotFoundException::class);

it('fails when load() is called and the storage is already loaded',function()
{
    $test = new DummyPersistentPoolStorage();
    $test->load(1);
    $test->load(2);
})->throws(StorageAlreadyLoadedException::class);

test('load() after reset() works', function()
{
    $test = new DummyPersistentPoolStorage();
    $test->load(1);
    $test->reset();
    $test->load(2);
    expect($test->getValue('str_field'))->toBe('GHI');
});