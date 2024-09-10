<?php

namespace Sunhill\Tests\Unit\Query;

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\Exceptions\InvalidOrderException;
use Sunhill\Query\Exceptions\UnknownFieldException;

uses(SunhillTestCase::class);

define('EXPECTED_QUERY', '0:');

test('Query with order() and unknown direction', function()
{
    $test = new DummyQuery();
    $test->orderBy('id','somewhere')->first();
    
})->throws(InvalidOrderException::class);

test('Query with order() and unknown field', function()
{
    $test = new DummyQuery();
    $test->orderBy('nofield')->first();
    
})->throws(UnknownFieldException::class);

test('Query with order() and unsortable field', function()
{
    $test = new DummyQuery();
    $test->orderBy('payload')->first();
    
})->throws(InvalidOrderException::class);

test('Query', function($callback, $expectation, $method = null)
{
    $test = new DummyQuery();
    if (is_callable($callback)) {
        $test = $callback($test);
    }
    if ($method) {
        $result = $method($test);
    } else {
        $result = $test->first()->payload;
    }
    expect($result)->toBe($expectation);    
})->with(
[
    'Simple query with count()'=>[
        null, 5, 
        function ($query)
        {
            return $query->count();
        }
    ],
    'Simple query with first()'=>[
        null, EXPECTED_QUERY
       
    ],
    'Simple query with first() and single field'=>[
        null, '',
        function($query)
        {
            return $query->first('payload');
        }
    ],
    'Simple query with first() and array of field'=>[
        null, '',
        function($query)
        {
            return $query->first(['id','payload'])->payload;
        }    
    ],
    'Query with get()'=>[
        null, EXPECTED_QUERY,
        function($query)
        {
            return $query->get()->first()->payload;
        }    
    ],
    'Query with get() with single field'=>[
        null, '',
        function($query)
        {
            return $query->get('payload')->first();
        }    
    ],
    'Query with get() with array of fields'=>[
        null, '',
        function($query)
        {
            return $query->get(['id','payload'])->first()->payload;
        }    
    ],
    'Query with offset()'=>[
        function($query)
        {
            return $query->offset(1);
        }, '0:offset:1',
        null    
    ],
    
    'Query with limit()'=>[
        function($query)
        {
            return $query->limit(2);
        }, '0:limit:2',
        null
    ],
    'Query with order() and default direction'=>[
        function($query)
        {
            return $query->orderBy('id');
        }, '0:order:iddir:asc',
        null    
    ],
    'Query with order() and given direction'=>[
        function($query)
        {
            return $query->orderBy('id','desc');
        }, '0:order:iddir:desc',
        null    
    ],
    'simple where'=>[
        function($query) 
        { 
            return $query->where('id','=',1); 
        },'0:where:[(and:id=1)]'
    ],
    'simple where with default'=>[
        function($query)
        {
            return $query->where('id',1);
        },'0:where:[(and:id=1)]'
    ],
    'combined where'=>[
        function($query)
        {
            return $query->where('id','=',1)->where('payload','=','abc');
        },'0:where:[(and:id=1),(and:payload=abc)]'
    ],       
    'combined where not'=>[
        function($query)
        {
            return $query->where('id','=',1)->whereNot('payload','=','abc');
        },'0:where:[(and:id=1),(andnot:payload=abc)]'
     ],
     'combined where or'=>[
         function($query)
         {
             return $query->where('id','=',1)->orWhere('payload','=','abc');
         },'0:where:[(and:id=1),(or:payload=abc)]'
      ],
      'combined where or not'=>[
          function($query)
          {
              return $query->where('id','=',1)->orWhereNot('payload','=','abc');
          },'0:where:[(and:id=1),(ornot:payload=abc)]'
      ],
      'combined where with nested conditions'=>[
          function($query)
          {
              return $query->where('id','=',1)->Where(function($query) { $query->where('payload','=','abc')->orWhere('payload','=','def'); });
          },'0:where:[(and:id=1),(and:[(and:payload=abc),(or:payload=def])]'
      ],
      'whereIn with array'=>[
          function($query)
          {
              return $query->whereIn('id',[1,2,3]); 
          },'0:where:[(and:id_in_[1,2,3])]'
      ],
      'whereIn with collection'=>[
          function($query)
          {
              $collection = collect([1,2,3]);
              return $query->whereIn('id',$collection);
          },'0:where:[(and:id_in_[1,2,3])]'
      ],
      'whereIn with query'=>[
          function($query)
          {
              $query = new DummyQuery();
              $query->where('payload','=','abc');
              return $query->whereIn('id',Query);
          },'0:where:[(and:id_in_[0:where:[(and:payload=abc)]])]'
       ],
          ]);

