<?php

/**
 * @file BooleanNodeTest.php
 * tests: /src/Parser/Nodes/BooleanNode.php
 * free of dependent units: yes
 */

use Sunhill\Parser\Nodes\BooleanNode;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

test('getDatatype()', function () {
    $test = new BooleanNode(true);
    expect($test->getDatatype())->toBe('boolean');
});

test('getValue()', function () {
    $test = new BooleanNode(true);
    expect($test->getValue())->toBe(true);
});

test('toString() with true', function () {
    $test = new BooleanNode(true);
    expect($test->toString())->toBe('true');
});

test('toString() with false', function () {
    $test = new BooleanNode(false);
    expect($test->toString())->toBe('false');
});

test('validate()', function () {
    $test = new BooleanNode(false);
    $test->validate();
    expect(true)->toBe(true);
});
