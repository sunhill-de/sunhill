<?php

uses(\Sunhill\Properties\Tests\TestCase::class);
use Sunhill\Properties\Properties\Exceptions\InvalidNameException;
use Sunhill\Properties\Tests\TestSupport\Properties\NonAbstractProperty;
test('names', function ($name, bool $forbidden) {
    if ($forbidden) {
        $this->expectException(InvalidNameException::class);
    }
    $test = new NonAbstractProperty();

    $test->setName($name);

    expect(true)->toBeTrue();
})->with('NamesProvider');
test('is valid property name', function ($name, bool $expect) {
    $test = new NonAbstractProperty();
    expect($test->isValidPropertyName($name))->toEqual(!$expect);
})->with('NamesProvider');
dataset('NamesProvider', function () {
    return [            
        ['_forbidden', true],
        ['string', true],
        ['object', true],
        ['float', true],
        ['integer', true],
        ['boolean', true],
        ['collection', true],
        ['name_with_underscores', false],
        ['namewith1digit', false],
        ['', true]
    ];
});
test('set name', function () {
    $test = new NonAbstractProperty();
    expect($test->getName())->toEqual('test_int');
    $test->setName('another');
    expect($test->getName())->toEqual('another');
});
test('force name', function () {
    $test = new NonAbstractProperty();

    $test->forceName('_test');

    expect($test->getName())->toEqual('_test');
});
test('additional getter', function ($item, $value) {
    $test = new NonAbstractProperty();
    $method = 'set_'.$item;
    $test->$method($value);
    $method = 'get_'.$item;
    expect($test->$method())->toEqual($value);
})->with('AdditionalGetterProvider');
dataset('AdditionalGetterProvider', function () {
    return [
        ['test','TEST'],
        ['Test','TEST'],
        ['_Test','TEST']
    ];
});