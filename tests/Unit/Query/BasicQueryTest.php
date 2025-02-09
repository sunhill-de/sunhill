<?php

namespace Sunhill\Tests\Unit\Query;

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\Exceptions\InvalidOrderException;
use Sunhill\Query\Exceptions\UnknownFieldException;

uses(SunhillTestCase::class);

test('assemble query', function($modification, $expectation) 
{
    $test = new DummyQuery();
    $modification($test);
    expect($test->assembled_query)->toBe($expectation);
})->with(
    [
        'simple first()'=>
        [
            function($query) { 
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
          "where:([and;a;=;0]),order:(),group:(),offset:(0),limit:(0)"
          ],
          'with a simple orWhere'=>
          [
              function($query)
              {
                  return $query->orWhere('a','=',0)->first();
          },
          "where:([or;a;=;0]),order:(),group:(),offset:(0),limit:(0)"
         ],
         'with a simple whereNot'=>
         [
             function($query)
             {
                 return $query->whereNot('a','=',0)->first();
         },
         "where:([andnot;a;=;0]),order:(),group:(),offset:(0),limit:(0)"
             ],
             'with a simple orWhere'=>
             [
                 function($query)
                 {
                     return $query->orWhereNot('a','=',0)->first();
             },
         "where:([ornot;a;=;0]),order:(),group:(),offset:(0),limit:(0)"
                 ],
                 ]);