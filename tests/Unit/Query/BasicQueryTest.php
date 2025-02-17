<?php

namespace Sunhill\Tests\Unit\Query;

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\Exceptions\InvalidOrderException;
use Sunhill\Query\Exceptions\UnknownFieldException;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;

uses(SunhillTestCase::class);

test('assemble query', function($modification, $expectation) 
{
    $test = new DummyQuery();
    $test->setStructure(DummyGrandChild::getExpectedStructure());
    
    $modification($test);
    expect($test->assembled_query)->toBe($expectation);
})->with(
    [
        'simple first()'=>
        [
                function($query) 
                { 
                    return $query->first(); 
                }, 
                'fields:(),where:(),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with some offset'=>
         [
                function($query) 
                {
                    return $query->offset(10)->first();
                },
                'fields:(),where:(),order:(),group:(),offset:(10),limit:(0)'
          ],
          'with some limit'=>
          [
                function($query) 
                {
                  return $query->limit(10)->first();
                },
                'fields:(),where:(),order:(),group:(),offset:(0),limit:(10)'
          ],
          'with a simple where'=>
          [
                function($query)
                {
                  return $query->where('dummyint','=',0)->first();
                },
                'fields:(),where:([and;dummyint;=;"0"]),order:(),group:(),offset:(0),limit:(0)'
          ],
          'with a simple orWhere'=>
          [
                function($query)
                {
                  return $query->orWhere('dummyint','=',0)->first();
                },
                'fields:(),where:([or;dummyint;=;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with a simple whereNot'=>
         [
                function($query)
                {
                    return $query->whereNot('dummyint','=',0)->first();
                },
                'fields:(),where:([andnot;dummyint;=;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with a simple orWhereNot'=>
         [
                function($query)
                {
                     return $query->orWhereNot('dummyint','=',0)->first();
                },
                'fields:(),where:([ornot;dummyint;=;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with a simple whereSomething'=>
         [
                    function($query)
                    {
                         return $query->whereSomething('dummyint',0)->first();
                    },
                    'fields:(),where:([and;dummyint;something;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with a function as a field'=>
         [
                    function($query)
                    {
                        return $query->where('function(dummyint)','=',0)->first();
                    },
                    'fields:(),where:([and;function( dummyint );=;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with a function with multiple argument'=>
         [
             function($query)
             {
                 return $query->where('function(a,b,c)','=',0)->first();
            },
            'fields:(),where:([and;function( a,b,c );=;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with a reference as a field'=>
         [
             function($query)
             {
                 return $query->where('a->b','=',0)->first();
             },
             'fields:(),where:([and;a -> b;=;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with a double reference as a field'=>
         [
             function($query)
             {
                 return $query->where('a->b->c','=',0)->first();
             },
             'fields:(),where:([and;a -> b -> c;=;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with a function of a reference as a field'=>
         [
             function($query)
             {
                 return $query->where('func(a->b)','=',0)->first();
             },
             'fields:(),where:([and;func( a -> b );=;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
             
         'with a function as a condition'=>
         [
             function($query)
             {
                 return $query->where('a','=','func(a)')->first();
             },
             "fields:(),where:([and;a;=;func( a )]),order:(),group:(),offset:(0),limit:(0)"
         ],
         'with a nested function as a condition'=>
         [
             function($query)
             {
                  return $query->where('a','=','func(sub(a))')->first();
             },
             "fields:(),where:([and;a;=;func( sub( a ) )]),order:(),group:(),offset:(0),limit:(0)"
         ],
         'with a function with multiple argument as condition'=>
         [
             function($query)
             {
                 return $query->where('a','=','func(a,b,c)')->first();
             },
            "fields:(),where:([and;a;=;func( a,b,c )]),order:(),group:(),offset:(0),limit:(0)"
         ],
         'with a reference as a condition'=>
         [
             function($query)
             {
                 return $query->where('a','=','a->b')->first();
             },
             "fields:(),where:([and;a;=;a -> b]),order:(),group:(),offset:(0),limit:(0)"
          ],
          'with a double reference as a condition'=>
          [
                function($query)
                {
                    return $query->where('a','=','a->b->c')->first();
                },
                "fields:(),where:([and;a;=;a -> b -> c]),order:(),group:(),offset:(0),limit:(0)"
          ],
            'with a function of a reference as a condition'=>
            [
                function($query)
                {
                    return $query->where('a','=','func(a->b)')->first();
                },
                "fields:(),where:([and;a;=;func( a -> b )]),order:(),group:(),offset:(0),limit:(0)"
            ],
            'with a string constant with double tics as a condition'=>
            [
                function($query)
                {
                    return $query->where('a','=','"abc"')->first();
                },
                'fields:(),where:([and;a;=;"abc"]),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with a string constant with single tics as a condition'=>
            [
                function($query)
                {
                    return $query->where('a','=',"'abc'")->first();
                },
                'fields:(),where:([and;a;=;"abc"]),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with a array as a condition'=>
            [
                function($query)
                {
                    return $query->where('a','=',[1,2,3])->first();
                },
                'fields:(),where:([and;a;=;[ 1,2,3 ]]),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with a collection as a condition'=>
            [
                function($query)
                {
                    $collect = collect([1,2,3]);
                    return $query->where('a','=',$collect)->first();
                },
                'fields:(),where:([and;a;=;[ 1,2,3 ]]),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with a query as a condition'=>
            [
                function($query)
                {
                    $subquery = new DummyQuery();
                    return $query->where('a','=',$subquery)->first();
                },
                'fields:(),where:([and;a;=;subquery]),order:(),group:(),offset:(0),limit:(0)'
            ],
                'with a closure as a condition'=>
            [
                function($query)
                {
                    return $query->where('a','=',function($query) { })->first();
                },
                'fields:(),where:([and;a;=;callback]),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with a assumed field name as a condition'=>
            [
                function($query)
                {
                    return $query->where('a','=','a')->first();
                },
                'fields:(),where:([and;a;=;a]),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with a assumed string constant as a condition'=>
            [
                function($query)
                {
                    return $query->where('a','=','abc')->first();
                },
                'fields:(),where:([and;a;=;"abc"]),order:(),group:(),offset:(0),limit:(0)'
            ],
                'with a assumed another string constant as a condition'=>
            [
                function($query)
                {
                    return $query->where('a','=','ab-cd')->first();
            },
            'fields:(),where:([and;a;=;"ab-cd"]),order:(),group:(),offset:(0),limit:(0)'
                ],
                
            'with string as fields'=>
            [
                function($query)
                {
                    return $query->fields('a')->first();    
                },        
                'fields:(a),where:(),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with string of list as fields'=>
            [
                function($query)
                {
                    return $query->fields('a,b,c')->first();
                },
                'fields:(a,b,c),where:(),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with array as fields'=>
            [
                function($query)
                {
                    return $query->fields(['a','b','c'])->first();
                },
                'fields:(a,b,c),where:(),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with collection as fields'=>
            [
                function($query)
                {
                    return $query->fields(collect(['a','b','c']))->first();
                },
                'fields:(a,b,c),where:(),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with function of field'=>
            [
                function($query)
                {
                    return $query->fields('func1(a),func2(b)')->first();
                },
                'fields:(func1( a ),func2( b )),where:(),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with callback'=>
            [
                function($query)
                {
                    return $query->fields(function($subquery) { })->first();
                },
                'fields:(callback),where:(),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with reference field'=>
            [
                function($query)
                {
                    return $query->fields('a->b,a->c')->first();
                },
                'fields:(a -> b,a -> c),where:(),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with subquery as fields'=>
            [
                function($query)
                {
                    return $query->fields(new DummyQuery())->first();
                },
                'fields:(subquery),where:(),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with a string as fields'=>
            [
                function($query)
                {
                    return $query->fields("abc,a,b")->first();
                },
                'fields:("abc",a,b),where:(),order:(),group:(),offset:(0),limit:(0)'
            ],
                
            'with a field as order statement'=>
            [
                function($query)
                {
                    return $query->order('a')->first();
                },
                'fields:(),where:(),order:([a,asc]),group:(),offset:(0),limit:(0)'
            ],
            'with a field as order statement'=>
            [
                function($query)
                {
                    return $query->order('a','desc')->first();
                },
                'fields:(),where:(),order:([a,desc]),group:(),offset:(0),limit:(0)'
            ],
            'with a callback as order statement'=>
            [
                function($query)
                {
                    return $query->order(function() { })->first();
                },
                'fields:(),where:(),order:([callback,asc]),group:(),offset:(0),limit:(0)'
            ],
                
                ]);

it('fails when ordering by a non field string', function()
{
    $test = \Mockery::mock(DummyQuery::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $test->shouldReceive('precheckQuery')->andReturn(true);
    $test->shouldReceive('hasProperty')->with('a')->andReturn(true);
    $test->shouldReceive('hasProperty')->andReturn(false);
    
    $test->order('abc')->first();
})->throws(InvalidOrderException::class);

it('fails when ordering by something else', function()
{
    $test = \Mockery::mock(DummyQuery::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $test->shouldReceive('precheckQuery')->andReturn(true);
    
    $test->order(12)->first();
})->throws(InvalidOrderException::class);

it('fails when direction is something else', function()
{
    $test = \Mockery::mock(DummyQuery::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $test->shouldReceive('precheckQuery')->andReturn(true);
    
    $test->order("a","something")->first();
})->throws(InvalidOrderException::class);


