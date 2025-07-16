<?php
/**
 * @file AbstractAttributeStorageTest.php
 * tests: /src/Attributes/AbstractAttributeStorage.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Tests\Unit\Attributes\Examples\DummyAttributeStorage;
use Sunhill\Attributes\Exceptions\AttributeNotAssignedException;
use Sunhill\Attributes\Exceptions\AttributeNotFoundException;

uses(SunhillTestCase::class);

test('loadAttribute() pass', function()
{
   $test = new DummyAttributeStorage();
   $attribute = $test->loadAttribute(2, 2);
   
   expect($attribute->name)->toBe('str_attribute');
   expect($attribute->type)->toBe('string');
   expect($attribute->value)->toBe('def');
});

test('loadAttribute() fail (no container)', function()
{
    
    $test = new DummyAttributeStorage();
    
    $test->loadAttribute(1000,1)->name;
})->throws(AttributeNotAssignedException::class);

test('loadAttribute() fail (no attribute)', function()
{
    
    $test = new DummyAttributeStorage();
    
    $test->loadAttribute(1,1000)->name;
})->throws(AttributeNotFoundException::class);

test('searchAttribute() with name pass', function()
{
    
    $test = new DummyAttributeStorage();
    
    expect($test->searchAttribute(['name'=>'str_attribute'])->id)->toBe(2);
});

test('searchAttribute() with name fail', function()
{
    
    $test = new DummyAttributeStorage();
    
    expect($test->searchAttribute(['name'=>'unknown']))->toBe(null);
});

test('storeAttribute works with new attribute', function()
{
    
    $test = new DummyAttributeStorage();
    
    $test->storeAttribute(2,999,'bce');
    expect($test::$attributes['str_attribute']['values'][999]['value'])->toBe('bce');
});

test('storeAttribute works with stored attribute', function()
{
    
    $test = new DummyAttributeStorage();
    
    $test->storeAttribute(2,1,'bce');
    expect($test::$attributes['str_attribute']['values'][1]['value'])->toBe('bce');
});

test('storeAttribute works with unknown attribute', function()
{
    
    $test = new DummyAttributeStorage();
    
    $test->storeAttribute(999,1,'bce');
})->throws(AttributeNotFoundException::class);

test('unsetAttribute() works', function()
{
    $test = new DummyAttributeStorage();
    
    $test->unsetAttribute(1,1);
    
    expect(isset($test::$attributes['test_attribute']['values'][1]))->toBe(false);
});

test('unsetAttribute fails with unknown attribute', function()
{
    
    $test = new DummyAttributeStorage();
    
    $test->unsetAttribute(999,1);
})->throws(AttributeNotFoundException::class);

