<?php

/**
 * @file CheckerTest.php
 * tests: /src/Checker/Checker.php
 * free of dependent units: yes
 */

namespace Sunhill\Tests\Unit\Checker;

use Sunhill\Checker\Exceptions\CheckException;
use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Tests\Unit\Checker\Examples\DummyChecker;

uses(SunhillSimpleTestCase::class);

/**
 * Tests: src/Checker/Checker::pass()
 */
test('Pass a check', function () {
    $test = new DummyChecker;
    $test->checkPass(false);
    expect($test->getLastResult())->toBe('passed');
});

/**
 * Tests: src/Checker/Checker::fail(), getLastResult(), getLastMessage()
 */
test('Failed a check', function () {
    $test = new DummyChecker;
    try {
        $test->checkFailure(false);
    } catch (CheckException $e) {
        expect($test->getLastResult())->toBe('failed');
        expect($test->getLastMessage())->toBe('FAILED');

        return;
    }
    expect(false)->toBe(true);
});

/**
 * Tests: src/Checker/Checker::repair(), getLastResult(), getLastMessage()
 */
test('Failed check with repair', function () {
    $test = new DummyChecker;
    try {
        $test->checkRepair(true);
    } catch (CheckException $e) {
        expect($test->getLastResult())->toBe('repaired');
        expect($test->getLastMessage())->toBe('REPAIRED');

        return;
    }
    expect(false)->toBe(true);
});

/**
 * Tests: src/Checker/Checker::unrepairable(), getLastResult(), getLastMessage()
 */
test('Failed check is unrepairable', function () {
    $test = new DummyChecker;
    try {
        $test->checkUnrepairable(true);
    } catch (CheckException $e) {
        expect($test->getLastResult())->toBe('unrepairable');
        expect($test->getLastMessage())->toBe('UNREPAIRABLE');

        return;
    }
    expect(false)->toBe(true);
});
