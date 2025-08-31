<?php

/**
 * @file TokenTest.php
 * tests: /src/Parser/Token.php
 * free of dependent units: yes
 */

use Sunhill\Parser\Token;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

test('Simple token', function () {
    $test = new Token('+');

    expect($test->getSymbol())->toBe('+');
});

test('Position', function () {
    $test = new Token('+');
    $test->setPosition(10, 15);

    expect($test->getLine())->toBe(10);
    expect($test->getColumn())->toBe(15);
});

test('Value', function () {
    $test = new Token('+');
    $test->setValue(10);

    expect($test->getValue())->toBe(10);
});

test('TypeHint', function () {
    $test = new Token('+');
    expect($test->getTypeHint())->toBe('unknown');
    $test->setTypeHint('int');

    expect($test->getTypeHint())->toBe('int');
});
