<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Storage\Exceptions\InvalidIDException;
use Sunhill\Storage\Exceptions\StorageAlreadyLoadedException;
use Sunhill\Tests\TestSupport\Storages\DummyPersistentSingleStorage;

uses(SimpleTestCase::class);

test("isInitialized() works when nothing loaded", function()
{
    $test = new DummyPersistentSingleStorage();
    expect($test->getIsInitialized('str_field'))->toBe(false);
});

test("load() works with isLoaded()", function()
{
    $test = new DummyPersistentSingleStorage();
    expect($test->isLoaded())->toBe(false);
    $test->load();
    expect($test->isLoaded())->toBe(true);
});

test("load() does its job", function()
{
    $test = new DummyPersistentSingleStorage();
    $test->load();
    expect($test->getValue('str_field'))->toBe('ABC');
});

test('load() does its jobs with arrays', function()
{
    $test = new DummyPersistentSingleStorage();
    $test->load();
    expect($test->getIndexedValue('array_field',1))->toBe(2);
    
});

test("isInitialized() works when something loaded", function()
{
    $test = new DummyPersistentSingleStorage();
    $test->load();
    expect($test->getIsInitialized('str_field'))->toBe(true);
});

it('fails when load() is called and the storage is already loaded',function()
{
    $test = new DummyPersistentSingleStorage();
    $test->load();
    $test->load();
})->throws(StorageAlreadyLoadedException::class);

test('load() after reset() works', function()
{
    $test = new DummyPersistentSingleStorage();
    $test->load();
    $test->reset();
    $test->load();
    expect($test->getValue('str_field'))->toBe('ABC');
});