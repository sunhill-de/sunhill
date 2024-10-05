<?php

uses(\Sunhill\Tests\TestCase::class);
use Sunhill\Facades\Properties;
use Sunhill\Properties\Exceptions\CantProcessPropertyException;
use Sunhill\Properties\Exceptions\DuplicateElementNameException;
use Sunhill\Tests\TestSupport\Properties\NonAbstractRecordProperty;
use Sunhill\Tests\TestSupport\Properties\NonAbstractProperty;
test('add by object', function () {
    $test = new NonAbstractRecordProperty();
    $element = new NonAbstractProperty();

    callProtectedMethod($test, 'addElement', ['test',$element]);

    expect($element->getName())->toEqual('test');
    expect($element->getOwner())->toEqual($test);
});
test('add by classname', function () {
    $test = new NonAbstractRecordProperty();

    $element = callProtectedMethod($test, 'addElement', ['test',NonAbstractProperty::class]);

    expect($element->getName())->toEqual('test');
    expect($element->getOwner())->toEqual($test);
});
test('add by property name', function () {
    $test = new NonAbstractRecordProperty();

    Properties::shouldReceive('isPropertyRegistered')->once()->with('test_property')->andReturn(true);
    Properties::shouldReceive('getPropertyNamespace')->once()->with('test_property')->andReturn(NonAbstractProperty::class);

    $element = callProtectedMethod($test, 'addElement', ['test', 'test_property']);

    expect($element->getName())->toEqual('test');
    expect($element->getOwner())->toEqual($test);
});
test('failed due dupplicate', function () {
    $test = new NonAbstractRecordProperty();
    $element = new NonAbstractProperty();

    $this->expectException(DuplicateElementNameException::class);

    callProtectedMethod($test, 'addElement', ['test',$element]);
    callProtectedMethod($test, 'addElement', ['test',$element]);
});
test('failed object', function () {
    $test = new NonAbstractRecordProperty();
    $this->expectException(CantProcessPropertyException::class);

    callProtectedMethod($test, 'addElement', ['test', new \StdClass()]);
});
test('failed classname', function () {
    $test = new NonAbstractRecordProperty();
    $this->expectException(CantProcessPropertyException::class);

    callProtectedMethod($test, 'addElement', ['test', \StdClass::class]);
});
test('failed property name', function () {
    $test = new NonAbstractRecordProperty();
    $this->expectException(CantProcessPropertyException::class);

    callProtectedMethod($test, 'addElement', ['test', 'unknown']);
});