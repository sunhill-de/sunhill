<?php

uses(\Sunhill\Properties\Tests\TestCase::class);

use Sunhill\Properties\Storage\AbstractCachedStorage;

class TestAbstractCachedStorage extends AbstractCachedStorage
{
    public $data = ['test_str'=>'ABC','test_int'=>123, 'test_array'=>['abc','def']];
    
    public $already_stored = false;
    
    function getReadCapability(string $name) : ?string
    {
        return null;
        // No need to test
    }
    
    function getIsReadable(string $name) : bool
    {
        return true;
    }
    
    function getIsWriteable(string $name) : bool
    {
        return true;
    }
    
    function doGetValue(string $name)
    {
        return $this->values[$name];
    }
    
    function getWriteCapability(string $name) : ?string
    {
        return null;
    }
    
    function getWriteable(string $name) : bool
    {
        return true;
    }
    
    function doGetIsInitialized(string $name) : bool
    {
        return true;
    }
    
    function getModifyCapability(string $name) : ?string
    {
        return null;
    }
    
    function doReadFromUnderlying()
    {
        $this->values = $this->data;
    }
    
    function doWriteToUnderlying()
    {
        $this->data = $this->values;
        $this->already_stored = true;
    }
    
    function doUpdateUnderlying()
    {
        $this->data = $this->values;
    }
    
    function isAlreadyStored() : bool
    {
        return $this->already_stored;
    }
}

test('read value', function () {
    $test = new TestAbstractCachedStorage();
    $test->already_stored = true;
    expect($test->getValue('test_str'))->toEqual('ABC');
});

test('write value', function () {
    $test = new TestAbstractCachedStorage();
    $test->setValue('test_str', 'DEF');
    expect($test->getValue('test_str'))->toEqual('DEF');
    expect($test->isDirty())->toBeTrue();
});

test('store value', function () {
    $test = new TestAbstractCachedStorage();
    $test->setValue('test_str', 'DEF');
    $test->commit();
    expect($test->data['test_str'])->toEqual('DEF');
    expect($test->isDirty())->toBeFalse();
});

test('update value', function () {
    $test = new TestAbstractCachedStorage();
    $test->already_stored = true;
    $test->setValue('test_str', 'DEF');
    $test->commit();
    expect($test->data['test_str'])->toEqual('DEF');
});

test('rollback', function () {
    $test = new TestAbstractCachedStorage();
    $test->already_stored = true;
    $test->setValue('test_str', 'DEF');
    expect($test->getValue('test_str'))->toEqual('DEF');
    $test->rollback();
    expect($test->getValue('test_str'))->toEqual('ABC');
});

test('array read', function () {
    $test = new TestAbstractCachedStorage();
    $test->already_stored = true;
    expect($test->getIndexedValue('test_array', 0))->toEqual('abc');
});

test('array append', function () {
    $test = new TestAbstractCachedStorage();
    $test->setIndexedValue('test_array', 2, 'ghi');
    expect($test->getIndexedValue('test_array', 2))->toEqual('ghi');
    expect($test->isDirty())->toBeTrue();
});

test('array change', function () {
    $test = new TestAbstractCachedStorage();
    $test->setIndexedValue('test_array', 1, 'ghi');
    expect($test->getIndexedValue('test_array', 1))->toEqual('ghi');
    expect($test->isDirty())->toBeTrue();
});
