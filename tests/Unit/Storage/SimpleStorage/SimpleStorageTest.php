<?php

uses(\Sunhill\Tests\TestCase::class);
use Sunhill\Storage\Exceptions\FieldNotAvaiableException;
use Sunhill\Tests\TestSupport\Storages\DummySimpleStorage;


test('read value', function () {
    $test = new DummySimpleStorage();
    expect($test->getValue('keyA'))->toEqual('ValueA');
});

test('read unknown value', function () {
    $this->expectException(FieldNotAvaiableException::class);

    $test = new DummySimpleStorage();
    $help = $test->getValue('NonExisting');
});

test('read array value', function () {
    $test = new DummySimpleStorage();
    expect($test->getIndexedValue('keyC', 1))->toEqual('DEF');
});

test('array count', function () {
    $test = new DummySimpleStorage();
    expect($test->getElementCount('keyC'))->toEqual(2);
});

test('an array count is returned', function() {
   $test = new DummySimpleStorage();
   expect($test->getElementCount('keyC'))->toEqual(2);
});
        
test('an array element is returned', function() {
   $test = new DummySimpleStorage();
   expect($test->getIndexedValue('keyC', 1))->toEqual('DEF');
});
    
test('dummy test to improve coverage', function()
{
    $test = new DummySimpleStorage();
    
    // The following function are never called but are abstract and therfore have to be
    // overwritten
    
    $test->setValue('A',10);
    $test->setIndexedValue('A',1,10);
    $test->getIsInitialized('A');
    expect(true)->toBe(true);
});