<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\Exceptions\InvalidTokenClassException;
use Sunhill\Query\Tokenizer;
use Sunhill\Query\Exceptions\InvalidTokenException;
use Sunhill\Query\Exceptions\UnexpectedTokenException;
use Sunhill\Query\QueryObject;
use Sunhill\Tests\Unit\Query\Examples\DummyQuery;

uses(SunhillTestCase::class);

test("No token detected", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $test = new Tokenizer($query_object);
    
    $test->parseParameter(new \stdClass,[
        'field',
        'const',
        'callback',
        'array_of_fields',
        'array_of_consts',
        'subquery',
        'function_of_field',
        'function_of_value',
    ]);
})->throws(InvalidTokenException::class);

test("Unexpected token detected", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $test = new Tokenizer($query_object);
    
    $test->parseParameter(10,[
        'field',
    ]);
})->throws(UnexpectedTokenException::class);
