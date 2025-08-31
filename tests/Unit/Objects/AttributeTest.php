<?php

/**
 * @file AttributeTest.php
 * tests: /src/Objects/ORMObject.php
 * free of dependent units: yes
 */

use Sunhill\Facades\Properties;
use Sunhill\Properties\Exceptions\PropertyNotFoundException;
use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Tests\TestSupport\Objects\Dummy;

uses(SunhillSimpleTestCase::class);

test('add an attribute', function ($type, $value, $throw) {
    Properties::shouldReceive('getAttributeID')->with('attribute')->andReturn(1);
    Properties::shouldReceive('getAttributeType')->with('attribute')->andReturn($type);
    $test = new Dummy;
    $thrown = false;
    try {
        $test->attribute = $value;
    } catch (\Sunhill\Properties\Exceptions\InvalidValueException $e) {
        $thrown = true;
    }
    expect($thrown)->toBe($throw);
})->group('attributes')->with(
    [
        ['string', 'abc', false],
        ['string', 1, false],
        ['integer', 'abc', true],
        ['integer', 1, false],
        ['integer', 1.23, true],
        ['float', 'abc', true],
        ['float', 1, false],
        ['float', 1.23, false],
        ['date', 'abc', true],
        ['date', '2025-05-05', false],
        ['date', '2025-05-05 21:22:23', false],
        ['time', 'abc', true],
        ['time', '21:22:23', false],
        ['time', '2025-05-05 21:22:23', false],
        ['datetime', 'abc', true],
        ['datetime', '2025-05-05 21:22:23', false],
        ['text', 'abc', false],

    ]);

test('Unknown attribute', function () {
    Properties::shouldReceive('getAttributeID')->with('attribute')->andReturn(null);
    $test = new Dummy;
    $test->attribute = 'abc';
})->group('attributes')->throws(PropertyNotFoundException::class);
