<?php

uses(\Sunhill\Tests\TestCase::class);

use Sunhill\Properties\Exceptions\NoStorageSetException;
use Sunhill\Properties\Exceptions\PropertyNotReadableException;
use Sunhill\Properties\Exceptions\UserNotAuthorizedForReadingException;
use Sunhill\Tests\TestSupport\TestUserManager;
use Sunhill\Properties\Exceptions\NoUserManagerInstalledException;
use Sunhill\Properties\Exceptions\PropertyNotWriteableException;
use Sunhill\Properties\Exceptions\UserNotAuthorizedForWritingException;
use Sunhill\Properties\Exceptions\UserNotAuthorizedForModifyException;
use Sunhill\Properties\Exceptions\InvalidValueException;
use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;
use Sunhill\Tests\TestSupport\Storages\TestAbstractIDStorage;
use Sunhill\Storage\AbstractStorage;

test('set storage', function () 
{
    $storage = \Mockery::mock(AbstractStorage::class);
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage);

    expect($test->getStorage())->toEqual($storage);
});

test('use createStorage()', function ()
{
    $storage = \Mockery::mock(AbstractStorage::class);
    
    $test = new NonAbstractProperty();
    $test->public_storage = $storage;
    
    expect($test->getStorage())->toEqual($storage);
});

test('no storage', function () {
    $test = new NonAbstractProperty();
    $test->setName('test');
    $test->getValue();
})->throws(NoStorageSetException::class);

test('get capabilities', function () {
    $test = new NonAbstractProperty();
    $test->setReadCapability('read');
    $test->setWriteCapability('write');
    $test->setModifyCapability('modify');
    expect($test->readCapability())->toEqual('read');
    expect($test->writeCapability())->toEqual('write');
    expect($test->modifyCapability())->toEqual('modify');
    expect($test->getReadCapability())->toEqual('read');
    expect($test->getWriteCapability())->toEqual('write');
    expect($test->getModifyCapability())->toEqual('modify');
});

        
test('getReadable() works', function() 
{
    
    $test = new NonAbstractProperty();
    $test->setReadable(false);
    
    expect($test->getReadable())->toBe(false);    
});

test('property not readable', function () {
    $storage = \Mockery::mock(AbstractStorage::class);
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setReadable(false);
    
    $test->getValue();
})->throws(PropertyNotReadableException::class);

test('no user manager installed', function () {
    $storage = \Mockery::mock(AbstractStorage::class);
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setReadCapability('read');
    $test->setUserManager('');

    $test->getValue();
})->throws(NoUserManagerInstalledException::class);

test('TestUserManager works as expected', function()
{
    $test = new TestUserManager();
    expect($test->hasCapability('something'))->toBe(false);
    expect($test->hasCapability('required'))->toBe(true);
});

test('user not authorized for reading', function () {
    $test = new NonAbstractProperty();
    $test->setUserManager(TestUserManager::class);
    $test->setReadCapability('something');
    
    $test->getValue();
})->throws(UserNotAuthorizedForReadingException::class);

test('user authorized for reading', function () {
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('getIsInitialized')->once()->andReturn(true);
    $storage->expects('getValue')->with('test_int')->once()->andReturn(10);
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setUserManager(TestUserManager::class);
    $test->setReadCapability('required');
    
    expect($test->getValue())->toEqual(10);
});

test('format for human', function () {
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('getIsInitialized')->once()->andReturn(true);
    $storage->expects('getValue')->with('test_int')->once()->andReturn(345);
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage);

    expect($test->getHumanValue())->toEqual('A345');
});

test('property not writeable', function () {
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('getIsInitialized')->once()->andReturn(false);
    
    $test = new NonAbstractProperty();
    $test->setWriteable(false);
    $test->setStorage($storage);
    
    $test->setValue(1234);
})->throws(PropertyNotWriteableException::class);

test('no user manager installed while writing', function () {
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('getIsInitialized')->once()->andReturn(false);
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setWriteCapability('something');
    $test->setUserManager('');
    
    $test->setValue(123);
})->throws(NoUserManagerInstalledException::class);

test('user not authorized for writing', function () {
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('getIsInitialized')->once()->andReturn(false);
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setWriteCapability('something');
    $test->setUserManager(TestUserManager::class);

    $test->setValue(123);
})->throws(UserNotAuthorizedForWritingException::class);

test('user authorized for writing', function () {
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('getIsInitialized')->once()->andReturn(false);
    $storage->expects('setValue')->with('test_int','Input123')->once();
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setWriteCapability('required');
    $test->setUserManager(TestUserManager::class);
    
    $test->setValue(123);
    expect(true)->toBeTrue();
});

test('user not authorized for modify', function () {
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('getIsInitialized')->once()->andReturn(true);
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setModifyCapability('something');
    $test->setUserManager(TestUserManager::class);

    $test->setValue(123);
})->throws(UserNotAuthorizedForModifyException::class);

test('no usermanager while modify', function () {
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('getIsInitialized')->once()->andReturn(true);
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setModifyCapability('something');
    $test->setUserManager('');

    $test->setValue(123);
})->throws(NoUserManagerInstalledException::class);

test('user authorized for modify', function () {
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('getIsInitialized')->once()->andReturn(true);
    $storage->expects('setValue')->with('test_int','Input123')->once()->andReturn(true);
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setUserManager(TestUserManager::class);
    $test->setModifyCapability('required');
    
    $test->setValue(123);
    expect(true)->toBeTrue();
});

test('property no writeable while modify', function () {
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('getIsInitialized')->once()->andReturn(true);
    
    $test = new NonAbstractProperty();
    $test->setWriteable(false);
    $test->setStorage($storage);
    expect($test->getWriteable())->toBe(false);
    expect($test->isWriteable())->toBe(false);
    $test->setValue(1234);
})->throws(PropertyNotWriteableException::class);

test('do set value', function () {
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('getIsInitialized')->once()->andReturn(true);
    $storage->expects('setValue')->with('test_int','Input123')->once()->andReturn(true);
    $storage->expects('getValue')->with('test_int')->once()->andReturn('Input123');
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage);

    $test->setValue(123);
    expect($storage->getValue('test_int'))->toEqual('Input123');
});

test('validate input', function () {
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('getIsInitialized')->once()->andReturn(false);
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->is_valid = false;

    $test->setValue(123);
})->throws(InvalidValueException::class);

test('set null as a value default behavior', function()
{
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('getIsInitialized')->once()->andReturn(false);
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    
    expect($test->getNullable())->toBe(false);
    $test->setValue(null);
})->throws(InvalidValueException::class);

test('setNullable() and getNullable()', function()
{
    $test = new NonAbstractProperty();
    $test->setNullable(true);
    expect($test->getNullable())->toBe(true);
});

test('notNullable()', function()
{
    $test = new NonAbstractProperty();
    $test->notNullable();
    expect($test->getNullable())->toBe(false);
});

test('getDefault() with no default', function()
{
    $test = new NonAbstractProperty();
    expect($test->getDefault())->toBe(null);
    expect($test->hasDefault())->toBe(false);
    expect($test->defaultsNull())->toBe(false);
});

test('setDefault() with a value', function()
{
    $test = new NonAbstractProperty();
    $test->setDefault(5);
    expect($test->getDefault())->toBe(5);
    expect($test->hasDefault())->toBe(true);
    expect($test->defaultsNull())->toBe(false);    
});

test('default() with a value', function()
{
    $test = new NonAbstractProperty();
    $test->default(5);
    expect($test->getDefault())->toBe(5);
    expect($test->hasDefault())->toBe(true);
    expect($test->defaultsNull())->toBe(false);
});

test('setDefault() with null', function()
{
    $test = new NonAbstractProperty();
    $test->setDefault(null);
    expect($test->getDefault())->toBe(null);
    expect($test->hasDefault())->toBe(true);
    expect($test->defaultsNull())->toBe(true);
    expect($test->getNullable())->toBe(true);
});

test('setNullable() works', function()
{
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('getIsInitialized')->twice()->andReturn(false);
    $storage->expects('setValue')->with('test_int',null)->once()->andReturn(true);
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setNullable(true);
    
    $test->setValue(null);
    expect($test->getValue())->toEqual(null);    
});

test('setNullable() set null as default', function()
{
    $storage = \Mockery::mock(AbstractStorage::class);
    $storage->expects('getIsInitialized')->once()->andReturn(false);
    
    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setNullable(true);
    
    expect($test->getValue())->toEqual(null);
});