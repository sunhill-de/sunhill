<?php

uses(SunhillTestCase::class);

test('Detect just a field', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('dummyint')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('dummyint')->andReturn('int');

    $test = new Parser($query_object);
    $result = $test->parse('dummyint');

    expect($result->type)->toBe('field');
    expect($result->field_type)->toBe('int');
    expect($result->value)->toBe('dummyint');
});

test('Detect just an integer', function()
{
    $query_object = \Mockery::mock(QueryObject::class);

    $test = new Parser($query_object);
    $result = $test->parse(123);

    expect($result->type)->toBe('const');
    expect($result->field_type)->toBe('int');
    expect($result->value)->toBe(123);
});

test('Detect just an integer as string', function()
{
    $query_object = \Mockery::mock(QueryObject::class);

    $test = new Parser($query_object);
    $result = $test->parse('123');

    expect($result->type)->toBe('const');
    expect($result->field_type)->toBe('int');
    expect($result->value)->toBe(123);
});

test('Detect just a float', function()
{
    $query_object = \Mockery::mock(QueryObject::class);

    $test = new Parser($query_object);
    $result = $test->parse(1.23);

    expect($result->type)->toBe('const');
    expect($result->field_type)->toBe('float');
    expect($result->value)->toBe(1.23);
});

test('Detect just a float as string', function()
{
    $query_object = \Mockery::mock(QueryObject::class);

    $test = new Parser($query_object);
    $result = $test->parse('123');

    expect($result->type)->toBe('const');
    expect($result->field_type)->toBe('float');
    expect($result->value)->toBe(1.23);
});

test('Detect just a string constant without quotes', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('abc')->andReturn(false);

    $test = new Parser($query_object);
    $result = $test->parse('abc');

    expect($result->type)->toBe('const');
    expect($result->field_type)->toBe('str');
    expect($result->value)->toBe('abc');
});

test('Detect just a string constant with single quotes', function()
{
    $query_object = \Mockery::mock(QueryObject::class);

    $test = new Parser($query_object);
    $result = $test->parse("'abc'");

    expect($result->type)->toBe('const');
    expect($result->field_type)->toBe('str');
    expect($result->value)->toBe('abc');
});

test('Detect just a string constant with double quotes', function()
{
    $query_object = \Mockery::mock(QueryObject::class);

    $test = new Parser($query_object);
    $result = $test->parse('"abc"');

    expect($result->type)->toBe('const');
    expect($result->field_type)->toBe('str');
    expect($result->value)->toBe('abc');
});

test('callback (with early call)', function()
{
    $query_object = \Mockery::mock(QueryObject::class);

    $test = new Parser($query_object);
    $result = $test->parse(function() { return 10; });

    expect($result->type)->toBe('const');
    expect($result->field_type)->toBe('int');
    expect($result->value)->toBe(10);
});

test('callback (without early call)', function()
{
    $query_object = \Mockery::mock(QueryObject::class);

    $test = new Parser($query_object);
    $result = $test->parse(function($query) { return $query; });

    expect($result->type)->toBe('callback');
});

test('function of const', function()
{
    $query_object = \Mockery::mock(QueryObject::class);

    $test = new Parser($query_object);
    $result = $test->parse('func(10)');

    expect($result->type)->toBe('function'); 
    expect($result->name)->toBe('func');
    expect($result->arguments[0]->type)->toBe('const');
    expect($result->arguments[0]->field_type)->toBe('int');
    expect($result->arguments[0]->value)->toBe(10);
});

test('function of field', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('dummyint')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('dummyint')->andReturn('int');

    $test = new Parser($query_object);
    $result = $test->parse('func(dummyint)');

    expect($result->type)->toBe('function'); 
    expect($result->name)->toBe('func');
    expect($result->arguments[0]->type)->toBe('field');
    expect($result->arguments[0]->field_type)->toBe('int');
    expect($result->arguments[0]->value)->toBe('dummyint');
});

