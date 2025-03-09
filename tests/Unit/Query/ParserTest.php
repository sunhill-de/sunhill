<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\QueryObject;
use Sunhill\Query\Parser;

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



test('list of constants', function()
{
    $query_object = \Mockery::mock(QueryObject::class);

    $test = new Parser($query_object);
    $result = $test->parse('10,20,30');

    expect($result->type)->toBe('list'); 
    expect(count($result->elements))->toBe(3);
    expect($result->elements[1]->type)->toBe('const');
    expect($result->elements[1]->field_type)->toBe('int');
    expect($result->elements[1]->value)->toBe(20);
});

test('list of constants as array', function()
{
    $query_object = \Mockery::mock(QueryObject::class);

    $test = new Parser($query_object);
    $result = $test->parse([10,20,30]);

    expect($result->type)->toBe('list'); 
    expect(count($result->elements))->toBe(3);
    expect($result->elements[1]->type)->toBe('const');
    expect($result->elements[1]->field_type)->toBe('int');
    expect($result->elements[1]->value)->toBe(20);
});

test('list of constants as collection', function()
{
    $query_object = \Mockery::mock(QueryObject::class);

    $test = new Parser($query_object);
    $result = $test->parse(collect([10,20,30]));

    expect($result->type)->toBe('list'); 
    expect(count($result->elements))->toBe(3);
    expect($result->elements[1]->type)->toBe('const');
    expect($result->elements[1]->field_type)->toBe('int');
    expect($result->elements[1]->value)->toBe(20);
});

test('list of fields', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('a')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('a')->andReturn('int');
    $query_object->shouldReceive('hasField')->once()->with('b')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('b')->andReturn('int');
    $query_object->shouldReceive('hasField')->once()->with('c')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('c')->andReturn('int');

    $test = new Parser($query_object);
    $result = $test->parse('a,b,c');

    expect($result->type)->toBe('list'); 
    expect(count($result->elements))->toBe(3);
    expect($result->elements[1]->type)->toBe('field');
    expect($result->elements[1]->field_type)->toBe('int');
    expect($result->elements[1]->value)->toBe('b');
});

test('list of fields with whitespaces', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('a')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('a')->andReturn('int');
    $query_object->shouldReceive('hasField')->once()->with('b')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('b')->andReturn('int');
    $query_object->shouldReceive('hasField')->once()->with('c')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('c')->andReturn('int');

    $test = new Parser($query_object);
    $result = $test->parse(' a , b , c ');

    expect($result->type)->toBe('list'); 
    expect(count($result->elements))->toBe(3);
    expect($result->elements[1]->type)->toBe('field');
    expect($result->elements[1]->field_type)->toBe('int');
    expect($result->elements[1]->value)->toBe('b');
});

test('list of fields as array', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('a')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('a')->andReturn('int');
    $query_object->shouldReceive('hasField')->once()->with('b')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('b')->andReturn('int');
    $query_object->shouldReceive('hasField')->once()->with('c')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('c')->andReturn('int');

    $test = new Parser($query_object);
    $result = $test->parse(['a','b','c']);

    expect($result->type)->toBe('list'); 
    expect(count($result->elements))->toBe(3);
    expect($result->elements[1]->type)->toBe('field');
    expect($result->elements[1]->field_type)->toBe('int');
    expect($result->elements[1]->value)->toBe('b');
});

test('list of fields as collection', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('a')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('a')->andReturn('int');
    $query_object->shouldReceive('hasField')->once()->with('b')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('b')->andReturn('int');
    $query_object->shouldReceive('hasField')->once()->with('c')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('c')->andReturn('int');

    $test = new Parser($query_object);
    $result = $test->parse(collect(['a','b','c']));

    expect($result->type)->toBe('list'); 
    expect(count($result->elements))->toBe(3);
    expect($result->elements[1]->type)->toBe('field');
    expect($result->elements[1]->field_type)->toBe('int');
    expect($result->elements[1]->value)->toBe('b');
});

test('list of mixed', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('a')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('a')->andReturn('int');
    $query_object->shouldReceive('hasField')->once()->with('b')->andReturn(false);

    $test = new Parser($query_object);
    $result = $test->parse('a,b,2,"c"');

    expect($result->type)->toBe('list'); 
    expect(count($result->elements))->toBe(3);
    expect($result->elements[0]->type)->toBe('field');
    expect($result->elements[0]->field_type)->toBe('int');
    expect($result->elements[0]->value)->toBe('a');
    expect($result->elements[1]->type)->toBe('const');
    expect($result->elements[1]->field_type)->toBe('str');
    expect($result->elements[1]->value)->toBe('b');
    expect($result->elements[2]->type)->toBe('const');
    expect($result->elements[2]->field_type)->toBe('int');
    expect($result->elements[2]->value)->toBe(2);
    expect($result->elements[3]->type)->toBe('const');
    expect($result->elements[3]->field_type)->toBe('str');
    expect($result->elements[3]->value)->toBe('c');
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

test('function without arguments', function()
{
    $query_object = \Mockery::mock(QueryObject::class);

    $test = new Parser($query_object);
    $result = $test->parse('func()');

    expect($result->type)->toBe('function'); 
    expect($result->name)->toBe('func');
    expect(count($result->arguments))->toBe(0);
});

test('function with list of arguments', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('a')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('a')->andReturn('int');
    $query_object->shouldReceive('hasField')->once()->with('b')->andReturn(false);

    $test = new Parser($query_object);
    $result = $test->parse('func(a,b)');

    expect($result->type)->toBe('function'); 
    expect($result->name)->toBe('func');
    expect(count($result->arguments))->toBe(2);
    expect($result->arguments[0]->type)->toBe('field');
    expect($result->arguments[0]->field_type)->toBe('int');
    expect($result->arguments[0]->value)->toBe('a');
    expect($result->arguments[1]->type)->toBe('const');
    expect($result->arguments[1]->field_type)->toBe('string');
    expect($result->arguments[1]->value)->toBe('b');
});

test('subquery', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $test = new Parser($query_object);

    $subquery = new DummyQuery();
    $result = $test->parse($subquery);

    expect($result->type)->toBe('subquery');
    expect($result->query)->toBe($subquery);
});

test('reference', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('a')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('a')->andReturn('reference');

    $test = new Parser($query_object);

    $result = $test->parse('a->b');

    expect($result->type)->toBe('reference');
    expect($result->name)->toBe('a');
    expect($result->reference->type)->toBe('field');
    expect($result->reference->name)->toBe('b');
});

test('double reference', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('a')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('a')->andReturn('reference');

    $test = new Parser($query_object);

    $result = $test->parse('a->b->c');

    expect($result->type)->toBe('reference');
    expect($result->name)->toBe('a');
    expect($result->reference->type)->toBe('reference');
    expect($result->reference->name)->toBe('b');
    expect($result->reference->reference->type)->toBe('field');
    expect($result->reference->reference->name)->toBe('b');
});

test('Operators', function($operator, $maps = null)
{
    if (!$maps) {
        $maps = $operator;
    }    
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('a')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('a')->andReturn('int');

    $test = new Parser($query_object);

    $result = $test->parse('a'.$operator.'10');

    expect($result->type)->toBe($maps);
    expect($result->left->type)->toBe('field');
    expect($result->left->name)->toBe('a');
    expect($result->right->type)->toBe('const');
    expect($result->right->name)->toBe(10);
})->with([
         ['+'],
         ['-'],
         ['*'],
         ['/'],
         ['%'],
         ['&'],
         ['|'],
         ['~'],
         ['<'],
         ['>'],
         ['<='],
         ['>='],
         ['==','='],
         ['='],
         ['!='],
         ['<>','!='],
        ]);

test('Operator sequece', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('a')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('a')->andReturn('int');

    $test = new Parser($query_object);

    $result = $test->parse('a+10+20');

    expect($result->type)->toBe('+');
    expect($result->left->type)->toBe('+');
    expect($result->left->left->name)->toBe('a');
    expect($result->left->right->value)->toBe(10);
    expect($result->right->value)->toBe(20);
});

test('Operator sequece (multiplication before addition)', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('a')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('a')->andReturn('int');

    $test = new Parser($query_object);

    $result = $test->parse('a+10*20');

    expect($result->type)->toBe('+');
    expect($result->left->name)->toBe('a');
    expect($result->right->type)->toBe('*');
    expect($result->right->left->value)->toBe(10);
    expect($result->right->right->value)->toBe(20);
});

test('Operator sequece (brackets come first)', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('hasField')->once()->with('a')->andReturn(true);
    $query_object->shouldReceive('getTypeOf')->once()->with('a')->andReturn('int');

    $test = new Parser($query_object);

    $result = $test->parse('(a+10)*20');

    expect($result->type)->toBe('*');
    expect($result->left->type)->toBe('+');
    expect($result->right->value)->toBe(20);
    expect($result->lift->left->name)->toBe('a');
    expect($result->lift->right->value)->toBe(10);
});
