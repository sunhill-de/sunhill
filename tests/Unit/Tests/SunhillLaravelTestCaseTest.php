<?php

use Sunhill\Tests\SunhillLaravelTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

uses(SunhillLaravelTestCase::class);

test('A simple test works', function()
{
    expect('a')->toBe('a');
});

test('sunhill helpers are installed', function()
{
    expect(function_exists('getScalarMessage'))->toBe(true);
});

test('Application is booted', function()
{
    expect(Schema::hasTable('notexistingtable'))->toBe(false);
});