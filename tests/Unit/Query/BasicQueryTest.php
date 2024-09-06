<?php

namespace Sunhill\Tests\Unit\Query;

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\Exceptions\InvalidOrderException;
use Sunhill\Query\Exceptions\UnknownFieldException;

uses(SunhillTestCase::class);

define('EXPECTED_QUERY', '0:');

test('Simple query with count()', function()
{
    $test = new DummyQuery();
    expect($test->count())->toBe(5);
});

test('Simple query with first()', function()
{
   $test = new DummyQuery();
   expect($test->first()->payload)->toBe(EXPECTED_QUERY);
});

test('Simple query with first() and single field', function()
{
    $test = new DummyQuery();
    expect($test->first('payload'))->toBe('');
});

test('Simple query with first() and array of field', function()
{
    $test = new DummyQuery();
    expect($test->first(['id','payload'])->payload)->toBe('');
});

test('Query with get()', function() 
{
    $test = new DummyQuery();
    expect($test->get()->first()->payload)->toBe(EXPECTED_QUERY);
});

test('Query with get() with single fiend', function()
{
    $test = new DummyQuery();
    expect($test->get('payload')->first())->toBe('');
});

test('Query with get() with array of fields', function()
{
    $test = new DummyQuery();
    expect($test->get(['id','payload'])->first()->payload)->toBe('');
});

test('Query with offset()', function()
{
   $test = new DummyQuery();
   expect($test->offset(1)->first()->payload)->toBe('0:offset:1');
});

test('Query with limit()', function()
{
    $test = new DummyQuery();
    expect($test->limit(2)->first()->payload)->toBe('0:limit:2');
});

test('Query with order() and default direction', function()
{
    $test = new DummyQuery();
    expect($test->orderBy('id')->first()->payload)->toBe('0:order:iddir:asc');    
});

test('Query with order() and given direction', function()
{
    $test = new DummyQuery();
    expect($test->orderBy('id','desc')->first()->payload)->toBe('0:order:iddir:desc');
});

test('Query with order() and unknown direction', function()
{
    $test = new DummyQuery();
    $test->orderBy('id','somewhere')->first();
    
})->throws(InvalidOrderException::class);

test('Query with order() and unknown field', function()
{
    $test = new DummyQuery();
    $test->orderBy('nofield')->first();
    
})->throws(UnknownFieldException::class);

test('Query with order() and unsortable field', function()
{
    $test = new DummyQuery();
    $test->orderBy('payload')->first();
    
})->throws(InvalidOrderException::class);

test('Query with where', function()
{
    $test = new DummyQuery();
    expect($test->where('id','=',1)->first()->payload)->toBe('0:where:[(id=1)]');    
});

