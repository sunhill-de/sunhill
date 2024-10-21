<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Storages\DummyAbstractPersistentStorage;

uses(SimpleTestCase::class);

test('commiting undirty does nothing', function()
{
    $test = new DummyAbstractPersistentStorage();
    $test->commit();
    expect($test->commited)->toBe(false);
});

test('adding value makes it dirty', function()
{
    $test = new DummyAbstractPersistentStorage();
    $test->setValue('new_value','DEF');
    
    expect($test->isDirty('new_value'))->toBe(true);
    expect($test->isDirty('array_value'))->toBe(false);
    expect($test->isDirty())->toBe(true);
    $mod = $test->pub_getModifiedValues();
    expect($mod['new_value']->old)->toBe(null);
    expect($mod['new_value']->new)->toBe('DEF');
    $test->commit();
    expect($test->commited)->toBe(true);
});

test('changing value makes it dirty', function()
{
   $test = new DummyAbstractPersistentStorage();
   $test->setValue('str_value','DEF');
   
   expect($test->isDirty('str_value'))->toBe(true);
   expect($test->isDirty('array_value'))->toBe(false);
   expect($test->isDirty())->toBe(true);
   $mod = $test->pub_getModifiedValues();
   expect($mod['str_value']->old)->toBe('ABC');
   expect($mod['str_value']->new)->toBe('DEF');
});

test('commiting calls doCommit()', function()
{
    $test = new DummyAbstractPersistentStorage();
    $test->setValue('str_value','DEF');
    
    expect($test->commited)->toBe(false);    
    $test->commit();
    expect($test->commited)->toBe(true);
    expect($test->isDirty())->toBe(false);
    
});

test('changing an array value makes it dirty', function()
{
    $test = new DummyAbstractPersistentStorage();
    $test->setIndexedValue('array_value',2,666);
    
    expect($test->isDirty('str_value'))->toBe(false);
    expect($test->isDirty('array_value'))->toBe(true);
    expect($test->isDirty())->toBe(true);    
    $mod = $test->pub_getModifiedValues();
    expect($mod['array_value']->old)->toBe([11,22,33]);
    expect($mod['array_value']->new)->toBe([11,22,666]);
});

test('appending an array value makes it dirty', function()
{
    $test = new DummyAbstractPersistentStorage();
    $test->setIndexedValue('array_value',null,666);
    
    expect($test->getIndexedValue('array_value',3))->toBe(666);
    expect($test->isDirty('str_value'))->toBe(false);
    expect($test->isDirty('array_value'))->toBe(true);
    expect($test->isDirty())->toBe(true);
    $mod = $test->pub_getModifiedValues();
    expect($mod['array_value']->old)->toBe([11,22,33]);
    expect($mod['array_value']->new)->toBe([11,22,33,666]);
});