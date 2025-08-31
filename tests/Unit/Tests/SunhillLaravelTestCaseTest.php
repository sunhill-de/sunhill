<?php

use Illuminate\Support\Facades\Schema;
use Sunhill\Tests\SunhillLaravelTestCase;

uses(SunhillLaravelTestCase::class);

test('A simple test works', function () {
    expect('a')->toBe('a');
});

test('sunhill helpers are installed', function () {
    expect(function_exists('getScalarMessage'))->toBe(true);
});

test('Application is booted', function () {
    expect(Schema::hasTable('notexistingtable'))->toBe(false);
});
