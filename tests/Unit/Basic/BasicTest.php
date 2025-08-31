<?php

/**
 * @file BasciTest.php
 * tests: /src/Basic/Base.php
 * free of dependent units: yes
 */

namespace Sunhill\Tests\Unit\Basic;

use Sunhill\Exceptions\SunhillException;
use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Tests\Unit\Basic\Examples\Extension;
use Sunhill\Tests\Unit\Basic\Examples\Extension2;
use Sunhill\Tests\Unit\Basic\Examples\Extension3;

uses(SunhillSimpleTestCase::class);

test('GetterSetter works', function () {
    $test = new Extension;
    $test->test = 2;
    expect($test->test)->toBe(2);
});

test('Excpetion is raised when writing non existing', function () {
    $test = new Extension;
    $test->notexisting = 2;
})->throws(SunhillException::class);

test('Excpetion is raised when reading non existing', function () {
    $test = new Extension;
    $a = $test->notexisting;
})->throws(SunhillException::class);

test('definesOwnMethod() works', function () {
    expect(Extension2::definesOwnMethod('ownMethod'))->toBe(true);
    expect(Extension3::definesOwnMethod('ownMethod'))->toBe(false);
});

test('definesOwnStaticMethod() works', function () {
    expect(Extension2::definesOwnMethod('ownStaticMethod'))->toBe(true);
    expect(Extension3::definesOwnMethod('ownStaticMethod'))->toBe(false);
});
