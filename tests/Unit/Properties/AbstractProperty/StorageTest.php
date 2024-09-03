<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Properties\Exceptions\NoStorageSetException;
use Sunhill\Properties\Properties\Exceptions\PropertyNotReadableException;
use Sunhill\Properties\Properties\Exceptions\UserNotAuthorizedForReadingException;
use Sunhill\Properties\Tests\TestSupport\TestUserManager;
use Sunhill\Properties\Properties\Exceptions\NoUserManagerInstalledException;
use Sunhill\Properties\Properties\Exceptions\PropertyNotWriteableException;
use Sunhill\Properties\Properties\Exceptions\UserNotAuthorizedForWritingException;
use Sunhill\Properties\Properties\Exceptions\UserNotAuthorizedForModifyException;
use Sunhill\Properties\Properties\Exceptions\InvalidValueException;
use Sunhill\Properties\Tests\TestSupport\Properties\NonAbstractProperty;
use Sunhill\Properties\Tests\TestSupport\Storages\TestAbstractIDStorage;
test('set storage', function () {
    $storage = new TestAbstractIDStorage();
    $storage->setID(1);

    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    expect($test->getStorage())->toEqual($storage);
    expect($test->getValue())->toEqual(345);
});
test('no storage', function () {
    $this->expectException(NoStorageSetException::class);
    $test = new NonAbstractProperty();

    $test->readCapability();
});
test('get capabilities', function () {
    $storage = new TestAbstractIDStorage();
    $storage->setID(1);
    $storage->read_capability = 'read';
    $storage->write_capability = 'write';
    $storage->modify_capability = 'modify';

    $test = new NonAbstractProperty();
    $test->setStorage($storage);

    expect($test->readCapability())->toEqual('read');
    expect($test->writeCapability())->toEqual('write');
    expect($test->modifyCapability())->toEqual('modify');
});
test('property no readable', function () {
    $storage = new TestAbstractIDStorage();
    $storage->is_readable = false;

    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setUserManager(TestUserManager::class);

    $this->expectException(PropertyNotReadableException::class);
    $test->getValue();
});
test('no user manager installed', function () {
    $storage = new TestAbstractIDStorage();
    $storage->read_capability = 'read';

    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setUserManager('');

    $this->expectException(NoUserManagerInstalledException::class);
    $test->getValue();
});
test('user not authorized for reading', function () {
    $storage = new TestAbstractIDStorage();
    $storage->read_capability = 'read';

    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setUserManager(TestUserManager::class);

    $this->expectException(UserNotAuthorizedForReadingException::class);
    $test->getValue();
});
test('user authorized for reading', function () {
    $storage = new TestAbstractIDStorage();
    $storage->read_capability = 'required';
    $storage->setID(1);

    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setUserManager(TestUserManager::class);

    expect($test->getValue())->toEqual(345);
});
test('format for human', function () {
    $storage = new TestAbstractIDStorage();
    $storage->setID(1);

    $test = new NonAbstractProperty();
    $test->setStorage($storage);

    expect($test->getHumanValue())->toEqual('A345');
});
test('property no writeable', function () {
    $storage = new TestAbstractIDStorage();
    $storage->is_writeable = false;

    $test = new NonAbstractProperty();
    $test->setStorage($storage);

    $this->expectException(PropertyNotWriteableException::class);
    $test->setValue(1234);
});
test('no user manager installed while writing', function () {
    $storage = new TestAbstractIDStorage();
    $storage->write_capability = 'write';

    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setUserManager('');

    $this->expectException(NoUserManagerInstalledException::class);
    $test->setValue(123);
});
test('user not authorized for writing', function () {
    $storage = new TestAbstractIDStorage();
    $storage->write_capability = 'read';

    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setUserManager(TestUserManager::class);

    $this->expectException(UserNotAuthorizedForWritingException::class);
    $test->setValue(123);
});
test('user authorized for writing', function () {
    $storage = new TestAbstractIDStorage();
    $storage->write_capability = 'required';
    $storage->setID(1);

    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setUserManager(TestUserManager::class);

    $test->setValue(123);
    expect(true)->toBeTrue();
});
test('user not authorized for modify', function () {
    $storage = new TestAbstractIDStorage();
    $storage->modify_capability = 'modify';
    $storage->setValue('test_int',455);

    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setUserManager(TestUserManager::class);

    $this->expectException(UserNotAuthorizedForModifyException::class);
    $test->setValue(123);
});
test('no usermanager while modify', function () {
    $storage = new TestAbstractIDStorage();
    $storage->modify_capability = 'required';
    $storage->setValue('test_int',455);

    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setUserManager('');

    $this->expectException(NoUserManagerInstalledException::class);
    $test->setValue(123);
});
test('user authorized for modify', function () {
    $storage = new TestAbstractIDStorage();
    $storage->modify_capability = 'required';
    $storage->setValue('test_int',455);

    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->setUserManager(TestUserManager::class);

    $test->setValue(123);
    expect(true)->toBeTrue();
});
test('property no writeable while modify', function () {
    $storage = new TestAbstractIDStorage();
    $storage->is_writeable = false;
    $storage->setValue('test_int',455);

    $test = new NonAbstractProperty();
    $test->setStorage($storage);

    $this->expectException(PropertyNotWriteableException::class);
    $test->setValue(1234);
});
test('do set value', function () {
    $storage = new TestAbstractIDStorage();
    $storage->setID(1);
    $test = new NonAbstractProperty();
    $test->setStorage($storage);

    $test->setValue(123);
    expect($storage->getValue('test_int'))->toEqual('Input123');
});
test('validate input', function () {
    $storage = new TestAbstractIDStorage();
    $storage->setID(1);
    $test = new NonAbstractProperty();
    $test->setStorage($storage);
    $test->is_valid = false;

    $this->expectException(InvalidValueException::class);

    $test->setValue(123);
});