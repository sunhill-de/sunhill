<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\QueryObject;
use Sunhill\Query\Checker;
use Sunhill\Query\Exceptions\InvalidStatementException;

uses(SunhillTestCase::class);

test('Where statement passes with field and simple relation', function() 
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFieldType')->with('a')->once()->andReturn('int');
    $query_object->shouldReceive('getFields')->andReturn([]);
    $query_object->shouldReceive('getWhereStatements')->andReturn([
        makeStdClass([
            'connect'=>'and',
            'field'=>makeStdClass(['type'=>'field','field'=>'a']),            
            'operator'=>'=',
            'condition'=>makeStdClass(['type'=>'const','value'=>1]),
        ])
    ]);
    $query_object->shouldReceive('getOrderStatements')->andReturn([]);
    $query_object->shouldReceive('getGroupFields')->andReturn([]);

    $test = new Checker($query_object);
    expect($test->check())->toBe(true);    
});

