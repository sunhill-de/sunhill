<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Properties\RecordProperty;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Tests\TestSupport\Storages\DummyStorage;
use Sunhill\Properties\Exceptions\PropertyNotFoundException;
use Sunhill\Tests\Feature\Properties\RecordProperty\Examples\SimpleRecordProperty;

uses(SunhillTestCase::class);

test('pass a callback via constructor', function()
{
    $storage = new DummyStorage();
    $test = new RecordProperty(function(ElementBuilder $builder) 
    {
        $builder->integer('test_int');
        $builder->string('test_str');
    });
    $test->setStorage($storage);
    
    $test->test_int = 10;
    $test->test_str = 'ABC';
    
    expect($test->test_int)->toBe(10);
    expect($test->test_str)->toBe('ABC');
});

it('fails when accessing an unknown property', function()
{
    $storage = new DummyStorage();
    $test = new RecordProperty(function(ElementBuilder $builder)
    {
        $builder->integer('test_int');
        $builder->string('test_str');
    });
    $test->setStorage($storage);
    
    $test->unknown = 10;
})->throws(PropertyNotFoundException::class);

test('pass a callback via setupRecord()', function()
{
    $storage = new DummyStorage();
    $test = new RecordProperty();
    $test->setupRecord(function(ElementBuilder $builder)
    {
        $builder->integer('test_int');
        $builder->string('test_str');
    });
    $test->setStorage($storage);
    
    $test->test_int = 10;
    $test->test_str = 'ABC';
    
    expect($test->test_int)->toBe(10);
    expect($test->test_str)->toBe('ABC');
});

test('use initializeRecord()', function()
{
    $storage = new DummyStorage();
    $test = new SimpleRecordProperty();
    $test->setStorage($storage);
    
    $test->test_int = 10;
    $test->test_str = 'ABC';
    
    expect($test->test_int)->toBe(10);
    expect($test->test_str)->toBe('ABC');    
});

test('use initializeRecord() and createStorage()', function()
{
    $test = new SimpleRecordProperty();
    
    $test->test_int = 10;
    $test->test_str = 'ABC';
    
    expect($test->test_int)->toBe(10);
    expect($test->test_str)->toBe('ABC');
});
