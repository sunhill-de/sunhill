<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Storage\Exceptions\FieldNotAvaiableException;
use Sunhill\Properties\Tests\TestSupport\Storages\DummyCallbackStorage;


test('read value', function () {
    $test = new DummyCallbackStorage();
    expect($test->getValue('readonly'))->toEqual('ABC');
});

it('fails when reading unknown value', function () {
    $test = new DummyCallbackStorage();
    $help = $test->getValue('NonExisting');
})->throws(FieldNotAvaiableException::class);

test('get readable on readonly', function () {
    $test = new DummyCallbackStorage();

    expect($test->getIsReadable('readonly'))->toBeTrue();
});

test('get writeable on readonly', function () {
    $test = new DummyCallbackStorage();

    expect($test->getIsWriteable('readonly'))->toBeFalse();
});

test('write readonly', function () {
    $this->expectException(FieldNotAvaiableException::class);

    $test = new DummyCallbackStorage();
    $test->setValue('readonly', 'RRR');
});

test('empty caps on readonly', function () {
    $test = new DummyCallbackStorage();

    expect($test->getReadCapability('readonly'))->toBeNull();
});

test('read read write', function () {
    $test = new DummyCallbackStorage();
    expect($test->getValue('readwrite'))->toEqual('DEF');
});

test('write read write', function () {
    $test = new DummyCallbackStorage();
    $test->setValue('readwrite','ZZZ');
    expect($test->readwrite_val)->toEqual('ZZZ');
});

test('get readable on readwrite', function () {
    $test = new DummyCallbackStorage();

    expect($test->getIsReadable('readwrite'))->toBeTrue();
});

test('get writeable on readwrite', function () {
    $test = new DummyCallbackStorage();

    expect($test->getIsWriteable('readwrite'))->toBeTrue();
});

test('get capabilities on restricted', function () {
    $test = new DummyCallbackStorage();

    expect($test->getWriteCapability('restricted'))->toEqual('write_cap');
});

    test('An uninitialized item is marked as uninitialized', function() {
        $test = new DummyCallbackStorage();
        
        expect($test->getIsInitialized('uninitialized'))->toBeFalse();        
    });
    
    
test('An uninitialized item is initialized after a value is assigned', function () {
    $test = new DummyCallbackStorage();

    $test->setValue('uninitialized','GHI');
    expect($test->getIsInitialized('uninitialized'))->toBeTrue();
});

test('An uninitialized item is initialized has a value after it is assigned', function () {
    $test = new DummyCallbackStorage();
        
    $test->setValue('uninitialized','GHI');
    expect($test->uninitialized_val)->toEqual('GHI');
});
    
test('an array count is returned', function() {
    $test = new DummyCallbackStorage();
    expect($test->getElementCount('arrayitem'))->toEqual(3);
});

test('an array element is returned', function() {
   $test = new DummyCallbackStorage();
   expect($test->getIndexedValue('arrayitem', 1))->toEqual('DEF');
});

test('an array element is writeable', function() {
    $test = new DummyCallbackStorage();
    $test->setIndexedValue('arrayitem', 1,'XXX');
    expect($test->arrayitem_val[1])->toEqual('XXX');
});