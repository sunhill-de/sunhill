<?php

/**
 * @file TimeNodeTest.php
 * tests: /src/Parser/Nodes/TimeNode.php
 * free of dependent units: yes
 */

use Sunhill\Parser\Nodes\TimeNode;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

test('getDatatype()', function () {
    $test = new TimeNode('11:12:13');
    expect($test->getDatatype())->toBe('time');
});

test('getValue()', function () {
    $test = new TimeNode('11:12:13');
    expect($test->getValue())->toBe('11:12:13');
});

test('toTime()', function () {
    $test = new TimeNode('11:12:13');
    expect($test->toString())->toBe('"11:12:13"');
});

test('validate()', function () {
    $test = new TimeNode('11:12:13');
    $test->validate();
    expect(true)->toBe(true);
});
