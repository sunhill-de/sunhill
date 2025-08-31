<?php

/**
 * @file SunhillSimpleTestCaseTest.php
 * tests: /tests/SunhillSimpleTestCase.php
 * free of dependent units: yes
 */

use Illuminate\Support\Facades\Schema;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

test('A simple test works', function () {
    expect('a')->toBe('a');
});
/*
This works only if this test is called alone not when called with other tests
test('sunhill helpers are not installed', function()
{
    expect(function_exists('makeStdClass'))->toBe(false);
});
*/

test('Application is not booted', function () {
    Schema::hasTable('notexistingtable');
})->throws(\Exception::class);
