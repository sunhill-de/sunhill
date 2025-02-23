<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\QueryObject;
use Sunhill\Query\Checker;
use Sunhill\Query\Exceptions\InvalidStatementException;

uses(SunhillTestCase::class);

test('Test QueryObject with a single field', function() 
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFields')->andReturn([makeStdClass(['type'=>'field','field'=>'a'])]);
    $query_object->shouldReceive('getWhereStatements')->andReturn([]);
    $query_object->shouldReceive('getOrderStatements')->andReturn([]);
    $query_object->shouldReceive('getGroupFields')->andReturn([]);

    $test = new Checker($query_object);
    expect($test->check())->toBe(true);    
});

test('Test QueryObject with a list of fields', function() 
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFields')->andReturn([
                                                         makeStdClass(['type'=>'field','field'=>'a']),
                                                         makeStdClass(['type'=>'field','field'=>'b']),
                                                                     ]);
    $query_object->shouldReceive('getWhereStatements')->andReturn([]);
    $query_object->shouldReceive('getOrderStatements')->andReturn([]);
    $query_object->shouldReceive('getGroupFields')->andReturn([]);

    $test = new Checker($query_object);
    expect($test->check())->toBe(true);    
});

test('Test QueryObject with a constant', function() 
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFields')->andReturn([
                                                         makeStdClass(['type'=>'const','value'=>'a']),
                                                         makeStdClass(['type'=>'field','field'=>'b']),
                                                                     ]);
    $query_object->shouldReceive('getWhereStatements')->andReturn([]);
    $query_object->shouldReceive('getOrderStatements')->andReturn([]);
    $query_object->shouldReceive('getGroupFields')->andReturn([]);

    $test = new Checker($query_object);
    expect($test->check())->toBe(true);    
});

test('Test QueryObject with a function of field', function() 
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFieldType')->with('a')->once()->andReturn('string');
    $query_object->shouldReceive('getFields')->andReturn([
                                                         makeStdClass(['type'=>'function_of_field','function'=>'upper','arguments'=>[makeStdClass(['type'=>'field','field'=>'a'])]]),
                                                         makeStdClass(['type'=>'field','field'=>'b']),
                                                                     ]);
    $query_object->shouldReceive('getWhereStatements')->andReturn([]);
    $query_object->shouldReceive('getOrderStatements')->andReturn([]);
    $query_object->shouldReceive('getGroupFields')->andReturn([]);

    $test = new Checker($query_object);
    expect($test->check())->toBe(true);    
});

                                                         
