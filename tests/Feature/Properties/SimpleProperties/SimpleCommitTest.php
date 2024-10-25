<?php

use Sunhill\Types\TypeVarchar;
use Sunhill\Tests\TestSupport\Storages\DummyPersistentSingleStorage;
use Sunhill\Facades\Properties;
use Sunhill\Tests\SunhillTestCase;

uses(SunhillTestCase::class);

test('getIsInitialized() works with initialized', function()
{
    $test = Properties::createProperty('varchar','str_field',DummyPersistentSingleStorage::class);
    $test->load();
    
    expect($test->isInitialized())->toBe(true);
});

test('getIsInitialized() works with uninitialized', function()
{
    $test = Properties::createProperty('varchar','unknown',DummyPersistentSingleStorage::class);
    
    expect($test->isInitialized())->toBe(false);
});

test('isDirty() works', function()
{
    $test = Properties::createProperty('varchar','str_field',DummyPersistentSingleStorage::class);
    
    expect($test->isDirty())->toBe(false);
    $test->setValue('DEF');
    expect($test->isDirty())->toBe(true);
});