<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Storage\Exceptions\FieldNotAvaiableException;
use Sunhill\Properties\Tests\TestSupport\Storages\DummySimpleStorage;


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
            