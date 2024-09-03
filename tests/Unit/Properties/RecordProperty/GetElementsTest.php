<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Tests\TestSupport\Properties\NonAbstractRecordProperty;
test('get element names', function () {
    $test = new NonAbstractRecordProperty();

    $elements = $test->getElementNames();

    expect($elements)->toEqual(['elementA','elementB']);
});

test('get own element names', function () {
    $test = new NonAbstractRecordProperty();

    $elements = $test->getOwnElementNames();

    expect($elements)->toEqual(['elementA','elementB']);
});

test('get elements', function () {
    $test = new NonAbstractRecordProperty();

    $elements = $test->getElementValues();

    expect($elements[0]->getName())->toEqual('elementA');
});

test('get own elements', function () {
    $test = new NonAbstractRecordProperty();

    $elements = $test->getOwnElementValues();

    expect($elements[0]->getName())->toEqual('elementA');
});

test('has element', function () {
    $test = new NonAbstractRecordProperty();

    expect($test->hasElement('elementA'))->toBeTrue();
});