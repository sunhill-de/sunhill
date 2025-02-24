<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\Exceptions\InvalidTokenClassException;
use Sunhill\Query\Tokenizer;
use Sunhill\Query\Exceptions\InvalidTokenException;
use Sunhill\Query\Exceptions\UnexpectedTokenException;
use Sunhill\Query\QueryObject;
use Sunhill\Tests\Unit\Query\Examples\DummyQuery;

uses(SunhillTestCase::class);

it('fails when a non defined tokenclass was passed', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $test = new Tokenizer($query_object);
    
    $test->parseParameter("test", ["non_existing_token"]);
})->throws(InvalidTokenClassException::class);

test("Detect a field", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('dummyint')->andReturn(true);
    
    $test = new Tokenizer($query_object);
    $result = $test->parseParameter("dummyint",['const','field']);
    
    expect($result->type)->toBe('field');
    expect($result->name)->toBe("dummyint");    
});

test("Detect a field with additional", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('dummyint')->andReturn(true);
    
    $test = new Tokenizer($query_object);
    $result = $test->parseParameter("dummyint",['const','field'],['key'=>'value']);
    
    expect($result->type)->toBe('field');
    expect($result->name)->toBe("dummyint");
    expect($result->key)->toBe("value");
});

test("Detect a const value", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter(10,['const']);
    
    expect($result->type)->toBe('const');
    expect($result->value)->toBe(10);
});

test("Detect a const float value", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter(1.5,['const']);
    
    expect($result->type)->toBe('const');
    expect($result->value)->toBe(1.5);
});

test("Detect a const string with double tics", function()
{
    $query_object = \Mockery::mock(QueryObject::class);    
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter('"abc"',['const','field']);
    
    expect($result->type)->toBe('const');
    expect($result->value)->toBe('abc');    
});

test("Detect a const string with single tics", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter("'abc'",['const','field']);
    
    expect($result->type)->toBe('const');
    expect($result->value)->toBe('abc');
});

test("Detect a const string without tics", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('abc')->andReturn(false);
    
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter("abc",['const','field']);
    
    expect($result->type)->toBe('const');
    expect($result->value)->toBe('abc');
});

test("Detect a const string as fieldname with tics", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter("'dummyint'",['const','field']);
    
    expect($result->type)->toBe('const');
    expect($result->value)->toBe('dummyint');
});

test("Callback with field as a result", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('abc')->andReturn(true);
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter(function() { return 'abc'; }, ['const','field']);
    
    expect($result->type)->toBe('field');
    expect($result->name)->toBe('abc');
});

test("Callback with late resolv direction", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter(function() { return 'abc'; }, ['const','field','callback'], [], false);
    
    expect($result->type)->toBe('callback');
});

test("list of fields", function() 
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('abc')->andReturn(true);
    $query_object->shouldReceive('hasField')->once()->with('def')->andReturn(true);

    $test = new Tokenizer($query_object);

    $result = $test->parseParameter("abc,def", ['const','field','array_of_fields']);
    expect($result->type)->toBe('array_of_fields');
    expect($result->elements[0]->type)->toBe('field');
    expect($result->elements[0]->field)->toBe('abc');
    expect($result->elements[1]->type)->toBe('field');
    expect($result->elements[1]->field)->toBe('def');    
});

test("array of fields", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('abc')->andReturn(true);
    $query_object->shouldReceive('hasField')->once()->with('def')->andReturn(true);
    
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter(["abc","def"], ['const','field','array_of_fields']);
    expect($result->type)->toBe('array_of_fields');
    expect($result->elements[0]->type)->toBe('field');
    expect($result->elements[0]->field)->toBe('abc');
    expect($result->elements[1]->type)->toBe('field');
    expect($result->elements[1]->field)->toBe('def');
});

test("list of fields with whitespaces", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('abc')->andReturn(true);
    $query_object->shouldReceive('hasField')->once()->with('def')->andReturn(true);
    
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter(" abc , def ", ['const','field','array_of_fields']);
    expect($result->type)->toBe('array_of_fields');
    expect($result->elements[0]->type)->toBe('field');
    expect($result->elements[0]->field)->toBe('abc');
    expect($result->elements[1]->type)->toBe('field');
    expect($result->elements[1]->field)->toBe('def');
});

test("list of consts", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('abc')->andReturn(false);
    $query_object->shouldReceive('hasField')->once()->with('def')->andReturn(false);
    
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter("abc,def", ['const','field','array_of_fields','array_of_consts']);
    expect($result->type)->toBe('array_of_consts');
    expect($result->elements[0]->type)->toBe('const');
    expect($result->elements[0]->value)->toBe('abc');
    expect($result->elements[1]->type)->toBe('const');
    expect($result->elements[1]->value)->toBe('def');
});

test("array of consts", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('abc')->andReturn(false);
    $query_object->shouldReceive('hasField')->once()->with('def')->andReturn(false);
    
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter(["abc","def"], ['const','field','array_of_fields','array_of_consts']);
    expect($result->type)->toBe('array_of_consts');
    expect($result->elements[0]->type)->toBe('const');
    expect($result->elements[0]->value)->toBe('abc');
    expect($result->elements[1]->type)->toBe('const');
    expect($result->elements[1]->value)->toBe('def');
});

test("function of field", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('abc')->andReturn(true);
    
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter("func( abc )", ['const','field','function_of_field']);
    
    expect($result->type)->toBe('function_of_field');
    expect($result->function)->toBe('func');
    expect($result->arguments[0]->type)->toBe('field');
});

test("function of fields", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('abc')->andReturn(true);
    $query_object->shouldReceive('hasField')->once()->with('def')->andReturn(true);
    
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter("func( abc , def)", ['const','field','function_of_field']);
    
    expect($result->type)->toBe('function_of_field');
    expect($result->function)->toBe('func');
    expect($result->arguments[0]->type)->toBe('field');
});

test("function of const", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->andReturn(false);
    
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter("func( 1 )", ['const','field','function_of_field','function_of_value']);
    
    expect($result->type)->toBe('function_of_value');
    expect($result->function)->toBe('func');
    expect($result->arguments[0]->type)->toBe('const');
});

test("function of consts", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->andReturn(false);
    
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter("func( 1,2 )", ['const','field','function_of_field','function_of_value']);
    
    expect($result->type)->toBe('function_of_value');
    expect($result->function)->toBe('func');
    expect($result->arguments[0]->type)->toBe('const');
    expect($result->arguments[1]->type)->toBe('const');
});

test("function of mixed", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('abc')->andReturn(true);
    $query_object->shouldReceive('hasField')->once()->with('1')->andReturn(false);
    
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter("func( abc, 1 )", ['const','field','function_of_field']);
    
    expect($result->type)->toBe('function_of_field');
    expect($result->function)->toBe('func');
    expect($result->arguments[0]->type)->toBe('field');
});

test("function without argument", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter("func( )", ['const','field','function_of_value']);
    
    expect($result->type)->toBe('function_of_value');
    expect($result->function)->toBe('func');
    expect($result->arguments)->toBe(null);
});

test("subquery", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    
    $test = new Tokenizer($query_object);
    $subquery = new DummyQuery();
    
    $result = $test->parseParameter($subquery, ['const','field','function_of_value','subquery']);
    expect($result->type)->toBe('subquery');
    expect($result->value)->toBe($subquery);
    
});

test("Reference", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    
    $test = new Tokenizer($query_object);
    $result = $test->parseParameter("reference->somekey",['const','field']);
    
    expect($result->type)->toBe('reference');
    expect($result->name)->toBe("reference");
    expect($result->key->type)->toBe("field");
    expect($result->key->field)->toBe("somekey");
});

test("Double reference", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    
    $test = new Tokenizer($query_object);
    $result = $test->parseParameter("reference->somekey->anotherkey",['const','field']);
    
    expect($result->type)->toBe('reference');
    expect($result->name)->toBe("reference");
    expect($result->key->type)->toBe("reference");
    expect($result->key->name)->toBe("somekey");
    expect($result->key->key->type)->toBe("field");
    expect($result->key->key->field)->toBe("anotherkey");
});

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

test("Additional field for parseParameter()", function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $test = new Tokenizer($query_object);
    
    $result = $test->parseParameter(10,['const'],['key'=>'value']);
    expect($result->key)->toBe('value');    
});
