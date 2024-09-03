<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Storage\Exceptions\FieldNotAvaiableException;
use Sunhill\Properties\Tests\TestSupport\Storages\DummySimpleWriteableStorage;


test('read value', function () {
    $test = new DummySimpleWriteableStorage();
    expect($test->getValue('keyA'))->toEqual('ValueA');
});

test('read unknown value', function () {
    $this->expectException(FieldNotAvaiableException::class);

    $test = new DummySimpleWriteableStorage();
    $help = $test->getValue('NonExisting');
});

test('overwrite', function () {
    $test = new DummySimpleWriteableStorage();
    $test->setValue('keyA','NewValue');

    expect($test->getValue('keyA'))->toEqual('NewValue');
});

test('writenew', function () {
    $test = new DummySimpleWriteableStorage();
    $test->setValue('keyC','NewValue');

    expect($test->getValue('keyC'))->toEqual('NewValue');
});

test('an array count is returned', function() {
   $test = new DummySimpleWriteableStorage();
   expect($test->getElementCount('keyC'))->toEqual(2);
});
        
test('an array element is returned', function() {
   $test = new DummySimpleWriteableStorage();
   expect($test->getIndexedValue('keyC', 1))->toEqual('DEF');
});
            
test('an array element is writeable', function() {
   $test = new DummySimpleWriteableStorage();
   $test->setIndexedValue('keyC', 1,'XXX');
   expect($test->values['keyC'][1])->toEqual('XXX');
});