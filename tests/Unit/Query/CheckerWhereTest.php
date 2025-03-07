<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\QueryObject;
use Sunhill\Query\Checker;
use Sunhill\Query\Exceptions\InvalidStatementException;

uses(SunhillTestCase::class);

function prepareQueryObject(array $field, $operator, array $condition)
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFields')->andReturn([]);
    $query_object->shouldReceive('getOrderStatements')->andReturn([]);
    $query_object->shouldReceive('getGroupFields')->andReturn([]);
    if ($field['type'] == 'field') {
        $query_object->shouldReceive('getFieldType')->with($field['field'])->atLeastOnce()->andReturn($field['resulttype']);
    }    
    if ($condition['type'] == 'field') {
        $query_object->shouldReceive('getFieldType')->with($condition['field'])->atLeastOnce()->andReturn($condition['resulttype']);
    }    
    $query_object->shouldReceive('getWhereStatements')->andReturn([
        makeStdClass([
            'connect'=>'and',
            'field'=>makeStdClass($field),            
            'operator'=>$operator,
            'condition'=>makeStdClass($condition),
        ])
    ]);
    return $query_object;
}

test('Where statement passes with field, relations and const', function($relation) 
{
    $test = new Checker(prepareQueryObject(['type'=>'field','field'=>'a','type'=>'int'],'=',['type'=>'const','value'=>1]));
    expect($test->check())->toBe(true);    
})->with([['='],['>'],['<'],['<='],['>='],['<>'],['!='],['<=>']]);

test('Where statement passes with field, equal relation and another field', function() 
{
    $test = new Checker(prepareQueryObject(['type'=>'field','field'=>'a','resulttype'=>'int'],'=',['type'=>'field','field'=>'b','resulttype'=>'int']));
    expect($test->check())->toBe(true);    
});

test('Where statement passes with field, relative relation and another field', function() 
{
    $test = new Checker(prepareQueryObject(['type'=>'field','field'=>'a','resulttype'=>'int'],'>',['type'=>'field','field'=>'b','resulttype'=>'int']));
    expect($test->check())->toBe(true);    
});

test('Where statement passes with function of field, simple relation and const', function() 
{
    $query_object = prepareQueryObject(['type'=>'function_of_field','function'=>'upper','arguments'=>makeStdClass(['type'=>'field','field'=>'a'])],'=',['type'=>'const','value'=>'ABC']);
    $query_object->shouldReceive('getFieldType')->with('a')->andReturn('string');
    $test = new Checker($query_object);
    expect($test->check())->toBe(true);    
});

test('Where statement fails with field, simple relation and type mismatching const', function() 
{
    $test = new Checker(prepareQueryObject(['type'=>'field','field'=>'a','resulttype'=>'int'],'=',['type'=>'const','value'=>'abc']));
    $test->check();    
})->throws(InvalidStatementException::class);

test('Where statement fails with field, invalid relation and const', function() 
{
    $test = new Checker(prepareQueryObject(['type'=>'field','field'=>'a','resulttype'=>'bool'],'>',['type'=>'const','value'=>0]));
    $test->check();    
})->throws(InvalidStatementException::class);

test('Where statement fails with field, unknown relation and const', function() 
{
    $test = new Checker(prepareQueryObject(['type'=>'field','field'=>'a','resulttype'=>'int'],'&%',['type'=>'const','value'=>0]));
    $test->check();    
})->throws(InvalidStatementException::class);

