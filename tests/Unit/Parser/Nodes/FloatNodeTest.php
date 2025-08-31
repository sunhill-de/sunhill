<?php

/**
 * @file FloatNodeTest.php
 * tests: /src/Parser/Nodes/FloatNode.php
 * free of dependent units: yes
 */

use Sunhill\Parser\Nodes\FloatNode;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

test('getDatatype()', function () {
    $test = new FloatNode(1.23);
    expect($test->getDatatype())->toBe('float');
});

test('getValue()', function () {
    $test = new FloatNode(1.23);
    expect($test->getValue())->toBe(1.23);
});

test('toString()', function () {
    $test = new FloatNode(1.23);
    expect($test->toString())->toBe('1.23');
});

test('validate()', function () {
    $test = new FloatNode(false);
    $test->validate();
    expect(1.23)->toBe(1.23);
});
