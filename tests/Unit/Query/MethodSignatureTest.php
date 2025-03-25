<?php

use Sunhill/Tests/SunhillTestCase;

uses(SunhillTestCase::class);

test('getSignature', function($parameter, $expect)
{
  expect(MethodSignature::getSignature($parameter))->toBe($expect);
})->with([
  [10,'integer'],
  [10.23,'float'],
  ["abc",'string'],
  [[1,2,3],'array of integer'],
  [[1.2,2.3,3.4],'array of float'],
  [[1,2.3,3],'array of float'],
  [["a","b","c"],'array of string'],
  [[1,1.2,"c"],'array of mixed'],         
]);         
       
