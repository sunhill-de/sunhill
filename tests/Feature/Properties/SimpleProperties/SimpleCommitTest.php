<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Storages\DummyStorage;
use Sunhill\Types\TypeVarchar;

uses(SimpleTestCase::class);

test('getIsInitialized() works with initialized', function()
{
    $storage = new DummyStorage();
    $test = new TypeVarchar();
    $test->setName('keyA')->setStorage($storage);
    
    expect($test->isInitialized())->toBe(true);
});

test('getIsInitialized() works with uninitialized', function()
{
    $storage = new DummyStorage();
    $test = new TypeVarchar();
    $test->setName('unknown')->setStorage($storage);
    
    expect($test->isInitialized())->toBe(false);
});

test('isDirty() works', function()
{
    $storage = new DummyStorage();
    $test = new TypeVarchar();
    $test->setName('keyA')->setStorage($storage);
    
    expect($test->isDirty())->toBe(false);
    $test->setValue('DEF');
    expect($test->isDirty())->toBe(true);
});