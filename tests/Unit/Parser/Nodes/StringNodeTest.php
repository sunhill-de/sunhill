<?php

/**
 * @file StringNodeTest.php
 * tests: /src/Parser/Nodes/StringNode.php
 * free of dependent units: yes
 */

use Sunhill\Parser\Nodes\StringNode;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

test('getDatatype()', function () {
    $test = new StringNode('abc');
    expect($test->getDatatype())->toBe('string');
});

test('getValue()', function () {
    $test = new StringNode('abc');
    expect($test->getValue())->toBe('abc');
});

test('toString()', function () {
    $test = new StringNode('abc');
    expect($test->toString())->toBe('"abc"');
});

test('validate()', function () {
    $test = new StringNode('abc');
    $test->validate();
    expect(true)->toBe(true);
});
