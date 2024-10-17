<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Storages\DummyPersistentSingleStorage;
use Sunhill\Storage\Exceptions\FieldNotAvaiableException;

uses(SimpleTestCase::class);

test('Write unknown value', function()
{
    $test = new DummyPersistentSingleStorage();
    $test->setValue('new_key','TEST');
    expect($test->getValue('new_key'))->toBe('TEST');
});

test('Write unknown value makes it dirty', function()
{
    $test = new DummyPersistentSingleStorage();
    $test->setValue('new_key','TEST');
    expect($test->isDirty('new_key'))->toBe(true);
});

test('isDirty() on unknown field fails', function()
{
    $test = new DummyPersistentSingleStorage();
    $test->isDirty('new_key');
})->throws(FieldNotAvaiableException::class);


test('First load then modify value', function()
{
    $test = new DummyPersistentSingleStorage();
    $test->load();
    $test->setValue('str_field','TEST');
    expect($test->getValue('str_field'))->toBe('TEST');    
});

test('First load then modify value makes it dirty', function()
{
    $test = new DummyPersistentSingleStorage();
    $test->load();
    expect($test->isDirty('str_field'))->toBe(false);
    $test->setValue('str_field','TEST');
    expect($test->isDirty('str_field'))->toBe(true);
});

test('First load then modify value to the same value doesnt makes it dirty', function()
{
    $test = new DummyPersistentSingleStorage();
    $test->load();
    expect($test->isDirty('str_field'))->toBe(false);
    $test->setValue('str_field','ABC');
    expect($test->isDirty('str_field'))->toBe(false);
});

test('First load then modify array value', function()
{
    $test = new DummyPersistentSingleStorage();
    $test->load();
    $test->setIndexedValue('array_field',1,666);
    expect($test->getIndexedValue('array_field',1))->toBe(666);
});

test('First load then modify array value makes it dirty', function()
{
    $test = new DummyPersistentSingleStorage();
    $test->load();
    expect($test->isDirty('array_field'))->toBe(false);
    $test->setIndexedValue('array_field',1,666);
    expect($test->isDirty('array_field'))->toBe(true);
});

