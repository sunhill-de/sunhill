<?php

/**
 * @file IntegerNodeTest.php
 * tests: /src/Parser/Nodes/IntegerNode.php
 * free of dependent units: yes
 */

use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

test('getDatatype()', function () {
    $test = new IntegerNode(123);
    expect($test->getDatatype())->toBe('integer');
});

test('getValue()', function () {
    $test = new IntegerNode(123);
    expect($test->getValue())->toBe(123);
});

test('toString()', function () {
    $test = new IntegerNode(123);
    expect($test->toString())->toBe('123');
});

test('validate()', function () {
    $test = new IntegerNode(123);
    $test->validate();
    expect(true)->toBe(true);
});
