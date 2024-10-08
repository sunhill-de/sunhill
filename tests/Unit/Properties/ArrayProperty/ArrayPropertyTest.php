<?php

/*
 * Tests src/Properties/ArrayProperty.php
 */

uses(\Sunhill\Tests\TestCase::class);

use Sunhill\Properties\Exceptions\InvalidParameterException;
use Sunhill\Types\TypeInteger;
use Sunhill\Types\TypeVarchar;
use Sunhill\Facades\Properties;
use Sunhill\Properties\Exceptions\InvalidValueException;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Properties\ArrayProperty;
use Sunhill\Properties\Exceptions\InvalidIndexException;
use Sunhill\Properties\Exceptions\InvalidIndexTypeException;
use Sunhill\Properties\MapProperty;

test('set allowed type', function ($types, $pass) {
    Properties::shouldReceive('isPropertyRegistered')->andReturn($pass);
    Properties::shouldReceive('getNamespaceOfProperty')->andReturn('Namesapce');
    if (!$pass) {
        $this->expectException(InvalidParameterException::class);
    }
    $test = new ArrayProperty();
    $test->setAllowedElementTypes($types);
    expect(true)->toBeTrue();
})->with([
    'single class with namespace'=>[TypeInteger::class, true],
    'list of classes with namespace'=>[[TypeInteger::class, TypeVarchar::class], true],
    'class id'=>['integer', true],
    'non existing class id'=>['notexisting', false],
    'wrong type'=>[3.3, false],
    'list of wrong types'=>[[3.3,4.3], false],
    'list with one wrong type'=>[[TypeInteger::class, 3.3], false],    
]);

test('return array element count', function () {
    $test = new ArrayProperty();
    $storage = Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('getIsInitialized')->once()->andReturn(true);
    $storage->shouldReceive('getElementCount')->once()->andReturn(2);
    $test->setStorage($storage);
    expect($test->count())->toEqual(2);
});

it('fails writing an array element with wrong type', function ($allowed, $value) {
    $test = new ArrayProperty();
    $test->setAllowedElementTypes($allowed);
    $test[] = $value;    
})->throws(InvalidValueException::class)->with(
    ['Wrong type'=>[TypeInteger::class, 'ABC']]
    );

test('read array element', function() {
    $test = new ArrayProperty();
    $test->setName('test');
    $storage = Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('getIsInitialized')->with('test')->once()->andReturn(true);
    $storage->shouldReceive('getIndexedValue')->with('test',1)->once()->andReturn(5);
    $test->setStorage($storage);
    expect($test[1], 5);
});

test('write array element', function ($allowed, $value) {
    $test = new ArrayProperty();
    $storage = Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setIndexedValue')->once();
    $test->setStorage($storage);
    $test->setAllowedElementTypes($allowed);
    $test[] = $value;
})->with([
    [null, 'ABC'],
    [TypeVarchar::class, 'ABC'],
    [TypeInteger::class, 123],
    [[TypeInteger::class, TypeVarchar::class], 'ABC'],
]);

test('Traversing an array element with integer indices', function() {
       $test = new ArrayProperty();
       $test->setName('test');
    
       $storage = Mockery::mock(AbstractStorage::class);
       $storage->shouldReceive('getIsInitialized')->with('test')->andReturn(true);
       $storage->shouldReceive('getElementCount')->with('test')->andReturn(2);
       $storage->shouldReceive('getIndices')->once()->with('test')->andReturn([0,1]);
       $storage->shouldReceive('getIndexedValue')->with('test',0)->once()->andReturn('ArrayElement');
       $storage->shouldReceive('getIndexedValue')->with('test',1)->once()->andReturn('ArrayElement');
       $test->setStorage($storage);
       
       $i = 0;
       foreach($test as $key => $element) {
           expect($key)->toBe($i++);
           expect($element)->toBe('ArrayElement');
       }
});

test('Traversing an array element with string indices', function() {
        $test = new ArrayProperty();
        $test->setName('test');
        
        $storage = Mockery::mock(AbstractStorage::class);
        $storage->shouldReceive('getIsInitialized')->with('test')->andReturn(true);
        $storage->shouldReceive('getElementCount')->with('test')->andReturn(2);
        $storage->shouldReceive('getIndices')->once()->with('test')->andReturn(['A','B']);
        $storage->shouldReceive('getIndexedValue')->with('test','A')->once()->andReturn('ArrayElement');
        $storage->shouldReceive('getIndexedValue')->with('test','B')->once()->andReturn('ArrayElement');
        $test->setStorage($storage);
        
        $i = 'A';
        foreach($test as $key => $element) {
            expect($key)->toBe($i++);
            expect($element)->toBe('ArrayElement');
        }
});
        
test('Unset an array element', function()
{
    $test = new ArrayProperty();
    $test->setName('test');

    $storage = Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('unsetOffset')->once()->with('test',1)->andReturn(true);    
    $test->setStorage($storage);
    
    $test->unset(1);
});

test('Null value as value for an array', function()
{
    $test = new ArrayProperty();
    $test->setName('test')->nullable();

    $storage = Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setValue')->once()->with('test',null)->andReturn(true);
    $storage->shouldReceive('getIsInitialized')->once()->with('test')->andReturn(false);
    $test->setStorage($storage);
    
    $test->setValue(null);
});

test('Assign an array to a array property', function()
{
    $test = new ArrayProperty();
    $test->setName('test');

    $storage = Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setValue')->once()->with('test',[1,2,3])->andReturn(true);
    $storage->shouldReceive('getIsInitialized')->once()->with('test')->andReturn(false);
    
    $test->setStorage($storage);
    
    $test->setValue([1,2,3]);
});

test('Assign an collection to a array property', function()
{
    $test = new ArrayProperty();
    $test->setName('test');
    
    $storage = Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setValue')->once()->with('test',[1,2,3])->andReturn(true);
    $storage->shouldReceive('getIsInitialized')->once()->with('test')->andReturn(false);
    
    $test->setStorage($storage);
    
    $collection = collect([1,2,3]);
    $test->setValue($collection);
});

test('Assign another array property to an array property', function()
{
    $test = new ArrayProperty();
    $test->setName('test');
    
    $storage = Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('setValue')->once()->with('test',[1,2,3])->andReturn(true);
    $storage->shouldReceive('getIsInitialized')->once()->with('test')->andReturn(false);
    
    $source_storage = Mockery::mock(AbstractStorage::class);
    $source_storage->shouldReceive('getIndexedValue')->once()->with('othertest',0)->andReturn(1);
    $source_storage->shouldReceive('getIndexedValue')->once()->with('othertest',1)->andReturn(2);
    $source_storage->shouldReceive('getIndexedValue')->once()->with('othertest',2)->andReturn(3);
    $source_storage->shouldReceive('getIsInitialized')->atLeast(1)->with('othertest')->andReturn(true);
    $source_storage->shouldReceive('getElementCount')->with('othertest')->andReturn(3);
    $source_storage->shouldReceive('getIndices')->once()->with('othertest')->andReturn([0,1,2]);
    
    $test->setStorage($storage);
    
    $source = new ArrayProperty();
    $source->setName('othertest');
    $source->setStorage($source_storage);
    
    $test->setValue($source);
});

test('MapProperty sets index type to string', function()
{
    $test = new MapProperty();
    expect($test->getIndexType())->toBe('string');
});

test('setIndexType() and getIndexType()', function()
{
    $test = new ArrayProperty();
    $test->setName('test')->setIndexType('string');
    expect($test->getIndexType())->toBe('string');
});

it('fails when setting a wronng index type', function()
{
    $test = new ArrayProperty();
    $test->setIndexType('something');
})->throws(InvalidIndexTypeException::class);

test('string index type acceps keys', function()
{
    $test = new ArrayProperty();
    $test->setName('test');
    $test->setIndexType('string');
    $test->setAllowedElementTypes(TypeInteger::class);
    
    $storage = Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('getIsInitialized')->with('test')->once()->andReturn(true);
    $storage->shouldReceive('getIndexedValue')->with('test','abc')->once()->andReturn(5);
    $storage->shouldReceive('setIndexedValue')->once()->with('test','abc',10);
    $test->setStorage($storage);
    expect($test['abc'], 5);
    $test['abc'] = 10;
});

it('failes when wrong index type is used', function()
{
    $test = new ArrayProperty();
    $test['abc'] = 10;
})->throws(InvalidIndexException::class);

test('offsetExists() works', function() 
{
    $test = new ArrayProperty();
    $test->setName('test');

    $storage = Mockery::mock(AbstractStorage::class);
    $storage->shouldReceive('getOffsetExists')->with('test',1)->once()->andReturn(true);
    
    $test->setStorage($storage);
    
    expect($test->offsetExists(1));
});