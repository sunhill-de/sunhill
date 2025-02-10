<?php

namespace Sunhill\Tests\Unit\Query;

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\Exceptions\InvalidOrderException;
use Sunhill\Query\Exceptions\UnknownFieldException;

uses(SunhillTestCase::class);

test('assemble query', function($modification, $expectation) 
{
    $test = \Mockery::mock(DummyQuery::class)->makePartial()->shouldAllowMockingProtectedMethods();
    $test->shouldReceive('precheckQuery')->andReturn(true);
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
                'where:(),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with some offset'=>
         [
                function($query) 
                {
                    return $query->offset(10)->first();
                },
                'where:(),order:(),group:(),offset:(10),limit:(0)'
          ],
          'with some limit'=>
          [
                function($query) 
                {
                  return $query->limit(10)->first();
                },
                'where:(),order:(),group:(),offset:(0),limit:(10)'
          ],
          'with a simple where'=>
          [
                function($query)
                {
                  return $query->where('a','=',0)->first();
                },
                'where:([and;a;=;"0"]),order:(),group:(),offset:(0),limit:(0)'
          ],
          'with a simple orWhere'=>
          [
                function($query)
                {
                  return $query->orWhere('a','=',0)->first();
                },
          'where:([or;a;=;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with a simple whereNot'=>
         [
                function($query)
                {
                    return $query->whereNot('a','=',0)->first();
                },
                'where:([andnot;a;=;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with a simple orWhereNot'=>
         [
                function($query)
                {
                     return $query->orWhereNot('a','=',0)->first();
                },
                'where:([ornot;a;=;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with a simple whereSomething'=>
         [
                    function($query)
                    {
                         return $query->whereSomething('a',0)->first();
                    },
                    'where:([and;a;something;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with a function as a field'=>
         [
                    function($query)
                    {
                        return $query->where('function(a)','=',0)->first();
                    },
                    'where:([and;function( a );=;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with a nested function as a field'=>
         [
             function($query)
             {
                 return $query->where('function(subfunc(a))','=',0)->first();
             },
             'where:([and;function( subfunc( a ) );=;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with a function with multiple argument'=>
         [
             function($query)
             {
                 return $query->where('function(a,b,c)','=',0)->first();
            },
            'where:([and;function( a,b,c );=;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with a reference as a field'=>
         [
             function($query)
             {
                 return $query->where('a->b','=',0)->first();
             },
             'where:([and;a -> b;=;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with a double reference as a field'=>
         [
             function($query)
             {
                 return $query->where('a->b->c','=',0)->first();
             },
             'where:([and;a -> b -> c;=;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
         'with a function of a reference as a field'=>
         [
             function($query)
             {
                 return $query->where('func(a->b)','=',0)->first();
             },
             'where:([and;func( a -> b );=;"0"]),order:(),group:(),offset:(0),limit:(0)'
         ],
             
         'with a function as a condition'=>
         [
             function($query)
             {
                 return $query->where('a','=','func(a)')->first();
             },
             "where:([and;a;=;func( a )]),order:(),group:(),offset:(0),limit:(0)"
         ],
         'with a nested function as a condition'=>
         [
             function($query)
             {
                  return $query->where('a','=','func(sub(a))')->first();
             },
             "where:([and;a;=;func( sub( a ) )]),order:(),group:(),offset:(0),limit:(0)"
         ],
         'with a function with multiple argument as condition'=>
         [
             function($query)
             {
                 return $query->where('a','=','func(a,b,c)')->first();
             },
            "where:([and;a;=;func( a,b,c )]),order:(),group:(),offset:(0),limit:(0)"
         ],
         'with a reference as a condition'=>
         [
             function($query)
             {
                 return $query->where('a','=','a->b')->first();
             },
             "where:([and;a;=;a -> b]),order:(),group:(),offset:(0),limit:(0)"
          ],
          'with a double reference as a condition'=>
          [
                function($query)
                {
                    return $query->where('a','=','a->b->c')->first();
                },
                "where:([and;a;=;a -> b -> c]),order:(),group:(),offset:(0),limit:(0)"
          ],
            'with a function of a reference as a condition'=>
            [
                function($query)
                {
                    return $query->where('a','=','func(a->b)')->first();
                },
                "where:([and;a;=;func( a -> b )]),order:(),group:(),offset:(0),limit:(0)"
            ],
            'with a string constant with double tics as a condition'=>
            [
                function($query)
                {
                    return $query->where('a','=','"abc"')->first();
                },
                'where:([and;a;=;"abc"]),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with a string constant with single tics as a condition'=>
            [
                function($query)
                {
                    return $query->where('a','=',"'abc'")->first();
                },
                'where:([and;a;=;"abc"]),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with a array as a condition'=>
            [
                function($query)
                {
                    return $query->where('a','=',[1,2,3])->first();
                },
                'where:([and;a;=;[ 1,2,3 ]]),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with a collection as a condition'=>
            [
                function($query)
                {
                    $collect = collect([1,2,3]);
                    return $query->where('a','=',$collect)->first();
                },
                'where:([and;a;=;[ 1,2,3 ]]),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with a query as a condition'=>
            [
                function($query)
                {
                    $query = new DummyQuery();
                    return $query->where('a','=',$query)->first();
                },
                'where:([and;a;=;query]),order:(),group:(),offset:(0),limit:(0)'
            ],
                'with a closure as a condition'=>
            [
                function($query)
                {
                    return $query->where('a','=',function($query) { })->first();
                },
                'where:([and;a;=;callback]),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with a assumed field name as a condition'=>
            [
                function($query)
                {
                    return $query->where('a','=','abc')->first();
                },
                'where:([and;a;=;abc),order:(),group:(),offset:(0),limit:(0)'
            ],
            'with a assumed field name as a condition'=>
            [
                function($query)
                {
                    return $query->where('a','=','ab-cd')->first();
            },
            'where:([and;a;=;"ab-cd"),order:(),group:(),offset:(0),limit:(0)'
                ],
                ]);