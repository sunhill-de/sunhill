<?php

use Sunhill\Tests\SimpleTestCase;
use Sunhill\Tests\TestSupport\Storages\DummyPersistentStorage;

uses(SimpleTestCase::class);

test('Committing a modified existing storage entry', function()
{
    $test = new DummyPersistentStorage();
    $test->load(1);
    $test->setValue('str_field','TEST');
    $test->commit();
    
    expect($test::$persistent_data[1]['str_field'])->toBe('TEST');
});

test('Committing a new storage entry', function()
{
    $test = new DummyPersistentStorage();
    $test->setValue('str_field','TEST');
    $test->setValue('int_field',123);
    $test->setValue('float_field',1.23);
    $test->setValue('array_field',[11,22,33]);
    $test->commit();
    
    expect($test->getID())->toBe(4);
    expect($test::$persistent_data[4]['str_field'])->toBe('TEST');
    expect($test::$persistent_data[4]['int_field'])->toBe(123);
    expect($test::$persistent_data[4]['float_field'])->toBe(1.23);
    expect($test::$persistent_data[4]['array_field'][0])->toBe(11);    
});

test('Rolling back a modified existing storage entry', function()
{
    $test = new DummyPersistentStorage();
    $test->load(1);
    $test->setValue('str_field','TEST');
    $test->rollback();
    expect($test->getValue('str_field'))->toBe('DEF');
    expect($test->isDirty())->toBe(false);
});
