<?php

use Sunhill\Properties\Storage\StaticStorage;
use Sunhill\Properties\Storage\Exceptions\FieldNotAvaiableException;

uses(\Sunhill\Properties\Tests\TestCase::class);

test('write and read simple value', function () {
    $test = new StaticStorage();
    $test->setValue('test','ABC');
    expect($test->getValue('test'))->toEqual('ABC');
});

test('write and read complex value', function () {
    $test = new StaticStorage();
    $test->setIndexedValue('test',0,'ABC');
    expect($test->getIndexedValue('test',0))->toEqual('ABC');
});

test('write and read complex value count', function () {
    $test = new StaticStorage();
    $test->setIndexedValue('test',0,'ABC');
    expect($test->getElementCount('test'))->toEqual(1);
});
        
it('raises an exception when a unset value is read', function() {
    $test = new StaticStorage();
    $test->getValue('nonexisting');
})->throws(FieldNotAvaiableException::class);

