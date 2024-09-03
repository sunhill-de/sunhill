<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Properties\Exceptions\InvalidParameterException;
use Sunhill\Properties\Types\TypeInteger;
use Sunhill\Properties\Types\TypeVarchar;
use Sunhill\Properties\Facades\Properties;
use Sunhill\Properties\Properties\Exceptions\InvalidValueException;
use Sunhill\Properties\Storage\AbstractStorage;
use Sunhill\Properties\Properties\ArrayProperty;

test('set allowed type', function ($types, $pass) {
    Properties::shouldReceive('isPropertyRegistered')->andReturn($pass);
    Properties::shouldReceive('getNamespaceOfProperty')->andReturn('Namesapce');
    if (!$pass) {
        $this->expectException(InvalidParameterException::class);
    }
    $test = new ArrayProperty();
    $test->setAllowedElementTypes($types);
    expect(true)->toBeTrue();
})->with('SetAllowedTypeProvider');
dataset('SetAllowedTypeProvider', function () {
    return [
        'single class with namespace'=>[TypeInteger::class, true],
        'list of classes with namespace'=>[[TypeInteger::class, TypeVarchar::class], true],
        'class id'=>['integer', true],
        'non existing class id'=>['notexisting', false],
        'wrong type'=>[3.3, false],
        'list of wrong types'=>[[3.3,4.3], false],
        'list with one wrong type'=>[[TypeInteger::class, 3.3], false],
    ];
});


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
})->with('WriteElementProvider');

dataset('WriteElementProvider', function () {
    return [ 
        [null, 'ABC'],
        [TypeVarchar::class, 'ABC'],
        [TypeInteger::class, 123],
        [[TypeInteger::class, TypeVarchar::class], 'ABC'],
    ];
});

    test('Traversing an array element', function() {
       $test = new ArrayProperty();
       $test->setName('test');
       $storage = Mockery::mock(AbstractStorage::class);
       $storage->shouldReceive('getIsInitialized')->with('test')->andReturn(true);
       $storage->shouldReceive('getElementCount')->with('test')->andReturn(2);
       $storage->shouldReceive('getIndexedValue')->with('test',0)->once()->andReturn('ArrayElement');
       $storage->shouldReceive('getIndexedValue')->with('test',1)->once()->andReturn('ArrayElement');
       $test->setStorage($storage);
       
       foreach($test as $key => $element) {
           expect($key, 'ArrayKey');
           expect($element, 'ArrayElement');
       }
    });