<?php

/**
 tests /tests/Pest.php
 */
use Sunhill\Tests\SimpleTestCase;

uses(SimpleTestCase::class);

class TestClass
{
    protected $protected_member = 10;
    
    protected function protectedMethod($param)
    {
        $old = $this->protected_member;
        $this->protected_member = $param;
        return $old;
    }
    
    public function getProtectedMember(): int
    {
        return $this->protected_member;
    }
}

test('getField() works', function($callback, $field, $expect)
{
    $test = $callback();
    expect(getField($test, $field))->toBe($expect);
})->with([
    [function() { return 10; }, null, 10],
    [function() 
    { 
        $return = new \StdClass();
        $return->subfield = 'ABC';
        return $return; 
    }, 'subfield', 'ABC'],
    [function()
    {
        $return = new \StdClass();
        $subfield = new \StdClass();
        $subfield->member = 'ABC';
        $return->subfield = $subfield;
        return $return;
    }, 'subfield->member', 'ABC'],
    [function()
    {
        return [1,2,3];
    }, '[1]', 2],
    [function()
    {
        return [[1,2,3],[4,5,6],[7.8,9]];
    }, '[1][1]', 5],
    [function()
    {
        $return = new \StdClass();
        $return->subfield = [1,2,3];
        return $return;
    }, 'subfield[1]', 2],
    [function()
    {
        $return = new \StdClass();
        $return->subfield =[[1,2,3],[4,5,6],[7.8,9]];
        return $return;
    }, 'subfield[1][1]', 5],
    ]);


test('callProtectedMethod() works', function()
{
    $test = new TestClass();
    $result = callProtectedMethod($test, 'protectedMethod',[20]);
    
    expect($result)->toBe(10);
    expect($test->getProtectedMember())->toBe(20);
});

test('invokeMethod() works', function()
{
    $test = new TestClass();
    $result = invokeMethod($test, 'protectedMethod',[20]);
    
    expect($result)->toBe(10);
    expect($test->getProtectedMember())->toBe(20);
});

test('getProtectedProperty() works', function()
{
    $test = new TestClass();
    
    expect(getProtectedProperty($test, 'protected_member'))->toBe(10);
});


test('setProtectedProperty() works', function()
{
    $test = new TestClass();
    
    setProtectedProperty($test, 'protected_member', 20);

    expect($test->getProtectedMember())->toBe(20);    
});

test('checkArrays works', function($first, $second, $expect)
{
    expect(checkArrays($first, $second))->toBe($expect);
})->with([
    [
        ['A','B','C'],['A','B','C'],true
    ],
    [
        ['A','B'],['A','B','C'],true,        
    ],
    [
        ['A','B','C'],['A','B'],false
    ],
]);

test('checkStdClasses works', function($first, $second, $expect, $except = [], $two_directions = false)
{
    $first_class = new \stdClass();
    $first_class = $first($first_class);
    
    $second_class = new \stdClass();
    $second_class = $second($second_class);
    
    expect(checkStdClasses($first_class, $second_class, $except, $two_directions))->toBe($expect);
})->with([
    'simple classes pass'=>[
        function(\stdClass $class) {
            $class->field1 = 123;
            return $class;
        },
        function(\stdClass $class) {
            $class->field1 = 123;
            return $class;
        }, true
     ],
     'simple classes fail due different value'=>[
         function(\stdClass $class) {
            $class->field1 = 123; 
            return $class;
         },
         function(\stdClass $class) {
             $class->field1 = 234;
             return $class;
         },false
     ],
     'simple classes fail due different keys'=>[
         function(\stdClass $class) {
             $class->field1 = 123;
             return $class;
         },
         function(\stdClass $class) {
             $class->field2 = 123;
             return $class;
         },false
      ],
      'simple classes pass with second more fields'=>[
          function(\stdClass $class) {
              $class->field1 = 123;
              return $class;
          },
          function(\stdClass $class) {
              $class->field1 = 123;
              $class->field2 = 234;
              return $class;
          },true
       ],
       'simple classes fail with first more fields'=>[
           function(\stdClass $class) {
               $class->field1 = 123;
               $class->field2 = 234;
               return $class;
           },
           function(\stdClass $class) {
               $class->field1 = 123;
               return $class;
           },false
       ],
       'simple classes pass with array'=>[
           function(\stdClass $class) {
               $class->field1 = [1,2,3];
               return $class;
           },
           function(\stdClass $class) {
               $class->field1 = [1,2,3];
               return $class;
           },true
        ],
        'simple classes fail with array missing element'=>[
            function(\stdClass $class) {
                $class->field1 = [1,2,3];
                return $class;
            },
            function(\stdClass $class) {
                $class->field1 = [1,2];
                return $class;
            },false
        ],
        'simple classes fail with array additional element'=>[
            function(\stdClass $class) {
                $class->field1 = [1,2,3];
                return $class;
            },
            function(\stdClass $class) {
                $class->field1 = [1,2,3,4];
                return $class;
            },false
         ],
         'simple classes pass with nested stdclass'=>[
             function(\stdClass $class) {
                 $class->field1 = new \stdClass();
                 $class->field1->subfield = 'ABC';
                 return $class;
             },
             function(\stdClass $class) {
                 $class->field1 = new \stdClass();
                 $class->field1->subfield = 'ABC';
                 return $class;
             },true
          ],
          'simple classes fail with nested stdclass'=>
          [
              function(\stdClass $class) {
                  $class->field1 = new \stdClass();
                  $class->field1->subfield = 'ABC';
                  return $class;
              },
              function(\stdClass $class) {
                  $class->field1 = new \stdClass();
                  $class->field1->subfield = 'DEF';
                  return $class;
              },false
           ],
           'simple classes pass with exception'=>
           [
               function(\stdClass $class) {
                   $class->field1 = 'ABC';
                   $class->field2 = 'DEF';
                   return $class;
               },
               function(\stdClass $class) {
                   $class->field1 = 'ABC';
                   $class->field2 = 'GHI';
                   return $class;
               },true,['field2']
           ],
           'simple classes fail with exception'=>
           [
               function(\stdClass $class) {
                   $class->field1 = 'ABC';
                   $class->field2 = 'DEF';
                   return $class;
               },
               function(\stdClass $class) {
                   $class->field1 = 'DEF';
                   $class->field2 = 'DEF';
                   return $class;
               },false,['field2']
            ],
            'simple classes pass with two direction'=>
            [
                function(\stdClass $class) {
                    $class->field1 = 'ABC';
                    $class->field2 = 'DEF';
                    return $class;
                },
                function(\stdClass $class) {
                    $class->field1 = 'ABC';
                    $class->field2 = 'DEF';
                    return $class;
                },true,[],true
            ],
            'simple classes fail with two direction'=>
            [
                function(\stdClass $class) {
                    $class->field1 = 'ABC';
                    $class->field2 = 'DEF';
                    return $class;
                },
                function(\stdClass $class) {
                    $class->field1 = 'ABC';
                    $class->field2 = 'DEF';
                    $class->field3 = 'GHI';
                    return $class;
                },false,[],true
            ],
                
]);
