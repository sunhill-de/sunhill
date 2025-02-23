<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\QueryObject;
use Sunhill\Query\Checker;
use Sunhill\Query\Exceptions\InvalidStatementException;

uses(SunhillTestCase::class);

// =========================== getTypeOf() =============================================
test('getTypeOf', function(\stdClass $test, string $expected)
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFieldType')->with('abc')->andReturn('string');
    
    $checker = new Checker($query_object);
    expect($checker->getTypeOf($test))->toBe($expected);
})->with(
    [
        'field'=>[makeStdClass(['type'=>'field','field'=>'abc']),'string'],
        'string const'=>[makeStdClass(['type'=>'const','value'=>'abc']),'string'],
        'integer const'=>[makeStdClass(['type'=>'const','value'=>50]),'int'],
        'float const'=>[makeStdClass(['type'=>'const','value'=>50.23]),'float'],
        'date const'=>[makeStdClass(['type'=>'const','value'=>'2023-12-31']),'date'],
        'datetime const'=>[makeStdClass(['type'=>'const','value'=>'2023-12-31 08:15:22']),'datetime'],
        'time const'=>[makeStdClass(['type'=>'const','value'=>'08:14:22']),'time'],
        'function'=>[makeStdClass(['type'=>'function_of_field','function'=>'sqrt']),'float'],
    ]
   );

// ============================== checkType =========================================
test('checkType', function(\stdClass $test, string $expected)
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFieldType')->with('abc')->andReturn('string');
    
    $checker = new Checker($query_object);
    expect($checker->checkType($expected, $test))->toBe(true);
})->with(
    [
        'field'=>[makeStdClass(['type'=>'field','field'=>'abc']),'string'],
        'string const'=>[makeStdClass(['type'=>'const','value'=>'abc']),'string'],
        'integer const'=>[makeStdClass(['type'=>'const','value'=>50]),'int'],
        'integer const for float'=>[makeStdClass(['type'=>'const','value'=>50]),'float'],
        'float const'=>[makeStdClass(['type'=>'const','value'=>50.23]),'float'],
        'date const'=>[makeStdClass(['type'=>'const','value'=>'2023-12-31']),'date'],
        'datetime const'=>[makeStdClass(['type'=>'const','value'=>'2023-12-31 08:15:22']),'datetime'],
        'time const'=>[makeStdClass(['type'=>'const','value'=>'08:14:22']),'time'],
        'function'=>[makeStdClass(['type'=>'function_of_field','function'=>'sqrt']),'float'],
        'array'=>[makeStdClass(['type'=>'array_of_fields']),'array'],
    ]
    );

test('fails when checkType is passed ', function(\stdClass $test, string $expected)
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFieldType')->with('abc')->andReturn('string');
    
    $checker = new Checker($query_object);
    expect($checker->checkType($expected, $test))->toBe(false);
})->with(
    [
        'string when expected int'=>[makeStdClass(['type'=>'field','field'=>'abc']),'int'],
        'float when expected int'=>[makeStdClass(['type'=>'const','value'=>5.5]),'int'],
    ]
    );

// ============================ checkFunction =========================================
test('Passing a function works', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFieldType')->with('a')->once()->andReturn('float');
    
    $test = new Checker($query_object);
    expect($test->checkFunction(makeStdClass([
        'type'=>'function_of_field',
        'function'=>'sqrt',
        'arguments'=>[makeStdClass([
            'type'=>'field',
            'field'=>'a'            
        ])]        
    ])))->toBe(true);    
});

test('Passing a ellipse function works with two field', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFieldType')->with('a')->once()->andReturn('string');
    $query_object->shouldReceive('getFieldType')->with('b')->once()->andReturn('string');
    
    $test = new Checker($query_object);
    expect($test->checkFunction(makeStdClass([
        'type'=>'function_of_field',
        'function'=>'concat',
        'arguments'=>[
            makeStdClass([
            'type'=>'field',
            'field'=>'a'
           ]),
            makeStdClass([
                'type'=>'field',
                'field'=>'b'
            ])
        ]
    ])))->toBe(true);
});

test('Passing a ellipse function works with two consts', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    
    $test = new Checker($query_object);
    expect($test->checkFunction(makeStdClass([
        'type'=>'function_of_field',
        'function'=>'concat',
        'arguments'=>[
            makeStdClass([
                'type'=>'const',
                'value'=>'abc'
            ]),
            makeStdClass([
                'type'=>'const',
                'value'=>'def'
            ])
        ]
    ])))->toBe(true);
});

test('Passing a ellipse function works with a field and a constant', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFieldType')->with('a')->once()->andReturn('string');
    
    $test = new Checker($query_object);
    expect($test->checkFunction(makeStdClass([
        'type'=>'function_of_field',
        'function'=>'concat',
        'arguments'=>[
            makeStdClass([
                'type'=>'field',
                'field'=>'a'
            ]),
            makeStdClass([
                'type'=>'const',
                'value'=>'b'
            ])
        ]
    ])))->toBe(true);
});

it('Fails when function does not exist in fields', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    
    $test = new Checker($query_object);
    $test->checkFunction(makeStdClass(['type'=>'function_of_field','function'=>'nonexisting','arguments'=>[makeStdClass(['type'=>'field','field'=>'a'])]]));
})->throws(InvalidStatementException::class);

it('Fails when function has to few parameters', function()
{
    $query_object = \Mockery::mock(QueryObject::class);

    $test = new Checker($query_object);
    $test->checkFunction(makeStdClass(['type'=>'function_of_field','function'=>'upper','arguments'=>[]]));
})->throws(InvalidStatementException::class);

it('Fails when function gets a field of wrong type', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFieldType')->with('a')->once()->andReturn('string');
    
    $test = new Checker($query_object);
    $test->checkFunction(makeStdClass(['type'=>'function_of_field','function'=>'sqrt','arguments'=>[makeStdClass(['type'=>'field','field'=>'a'])]]));
})->throws(InvalidStatementException::class);



// ========================== Other Tests ========================================
test('Test simple (empty) query pass', function()
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFields')->andReturn([]);
    $query_object->shouldReceive('getWhereStatements')->andReturn([]);
    $query_object->shouldReceive('getOrderStatements')->andReturn([]);
    $query_object->shouldReceive('getGroupFields')->andReturn([]);

    $test = new Checker($query_object);
    expect($test->check())->toBe(true);
});

