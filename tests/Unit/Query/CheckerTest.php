<?php

use Sunhill\Tests\SunhillTestCase;

uses(SunhillTestCase::class);

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

test('Test QueryObject with a single field', function() 
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFields')->andReturn([makeStdClass(['type'=>'field','field'=>'a']);
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
                                                         makeStdClass(['type'=>'field','field'=>'a'],
                                                         makeStdClass(['type'=>'field','field'=>'b'],
                                                                     );
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
                                                         makeStdClass(['type'=>'const','value'=>'a'],
                                                         makeStdClass(['type'=>'field','field'=>'b'],
                                                                     );
    $query_object->shouldReceive('getWhereStatements')->andReturn([]);
    $query_object->shouldReceive('getOrderStatements')->andReturn([]);
    $query_object->shouldReceive('getGroupFields')->andReturn([]);

    $test = new Checker($query_object);
    expect($test->check())->toBe(true);    
});

test('Test QueryObject with a function of field', function() 
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFieldType')->width('a')->once()->andReturn('string');
    $query_object->shouldReceive('getFields')->andReturn([
                                                         makeStdClass(['type'=>'funct','function'=>'uppercase','arguments'=>[makeStdClass(['type'=>'field','field'=>'a'])],
                                                         makeStdClass(['type'=>'field','field'=>'b'],
                                                                     );
    $query_object->shouldReceive('getWhereStatements')->andReturn([]);
    $query_object->shouldReceive('getOrderStatements')->andReturn([]);
    $query_object->shouldReceive('getGroupFields')->andReturn([]);

    $test = new Checker($query_object);
    expect($test->check())->toBe(true);    
});

it('Fails when function does not exist in fields', function() 
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFields')->andReturn([
                                                         makeStdClass(['type'=>'funct','function'=>'nonexisting','arguments'=>[makeStdClass(['type'=>'field','field'=>'a'])],
                                                         makeStdClass(['type'=>'field','field'=>'b'],
                                                                     );
    $query_object->shouldReceive('getWhereStatements')->andReturn([]);
    $query_object->shouldReceive('getOrderStatements')->andReturn([]);
    $query_object->shouldReceive('getGroupFields')->andReturn([]);

    $test = new Checker($query_object);
    $test->check();    
})->throws(InvalidStatementException::class);

it('Fails when function gets a field of wrong type', function() 
{
    $query_object = \Mockery::mock(QueryObject::class);
    $query_object->shouldReceive('getFieldType')->width('a')->once()->andReturn('string');
    $query_object->shouldReceive('getFields')->andReturn([
                                                         makeStdClass(['type'=>'funct','function'=>'sqrt','arguments'=>[makeStdClass(['type'=>'field','field'=>'a'])],
                                                         makeStdClass(['type'=>'field','field'=>'b'],
                                                                     );
    $query_object->shouldReceive('getWhereStatements')->andReturn([]);
    $query_object->shouldReceive('getOrderStatements')->andReturn([]);
    $query_object->shouldReceive('getGroupFields')->andReturn([]);

    $test = new Checker($query_object);
    $test->check();    
})->throws(InvalidStatementException::class);

                                                         
