<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\Exceptions\InvalidTokenClassException;
use Sunhill\Query\Tokenizer;
use Sunhill\Tests\Unit\Query\DummyQuery;
use Sunhill\Query\Exceptions\InvalidTokenException;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Query\Exceptions\UnexpectedTokenException;

uses(SunhillTestCase::class);

it('fails when a non defined tokenclass was passed', function()
{
    $test = new Tokenizer(new \stdClass());
    $test->parseParameter("test", ["non_existing_token"]);
})->throws(InvalidTokenClassException::class);

test("Token is detected", function($parameter, $expect)
{
    $test = new Tokenizer(DummyChild::getExpectedStructure());
    $result = $test->parseParameter($parameter, [
        'field',
        'const',
        'callback',
        'array_of_fields',
        'array_of_constants',
        'subquery',
        'function_of_field',
        'function_of_value',
    ]);
    expect($result->type)->toBe($expect);
})->with([
    [10,'const'],
    ['dummyint','field'],
    ['something','const'],
    [3.2,'const'],
    ['"something"','const'],
    [function() {},'callback'],
    ['func(dummyint)','function_of_field'],
    ['func(10)','function_of_value'],
    [new DummyQuery(),'subquery'],
    [[1,2,3],'array_of_constants'],
    [['dummyint','dummychildint'],'array_of_fields'],
    ["dummyint,dummychildint",'array_of_fields'],
    ["'dummyint'",'const'],
    ['"dummyint"','const']
]);

test("No token detected", function()
{
    $test = new Tokenizer(DummyChild::getExpectedStructure());
    $test->parseParameter(new \stdClass,[
        'field',
        'const',
        'callback',
        'array_of_fields',
        'array_of_constants',
        'subquery',
        'function_of_field',
        'function_of_value',
    ]);
})->throws(InvalidTokenException::class);

test("Unexpected token detected", function()
{
    $test = new Tokenizer(DummyChild::getExpectedStructure());
    $test->parseParameter(10,[
        'field',
    ]);
})->throws(UnexpectedTokenException::class);

