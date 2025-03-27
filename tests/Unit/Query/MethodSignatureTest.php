<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\Helpers\MethodSignature;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Query\Query;

uses(SunhillTestCase::class);

test('getSignature', function($parameter, $expect)
{
  expect(MethodSignature::getSignature($parameter))->toBe($expect);
})->with([
    [10,'integer'],
    [10.23,'float'],
    ["abc",'string'],
    [true, 'boolean'],
    [[1,2,3],'array of integer'],
    [[1.2,2.3,3.4],'array of float'],
    [[1,2.3,3],'array of float'],
    [["a","b","c"],'array of string'],
    [[1,1.2,"c"],'array of mixed'],
    [collect([1,2,3]),'array of integer'],
    [collect([1.2,2.3,3.4]),'array of float'],
    [collect([1,2.3,3]),'array of float'],
    [collect(["a","b","c"]),'array of string'],
    [collect([1,1.2,"c"]),'array of mixed'],
    [function() { return 12; },'callback'], 
]);         

test('getSignature with Dummy', function()
{
    $test = new Dummy();
    expect(MethodSignature::getSignature($test))->toBe('record');
});

test('getSignature with Query', function()
{
    $test = new Query();
    expect(MethodSignature::getSignature($test))->toBe('subquery');
});

test('getSignature with other object', function()
{
    $test = new \stdClass();
    expect(MethodSignature::getSignature($test))->toBe('object');
});

test('matches', function($callback, $params, $expect)
{
    $test = new MethodSignature();
    $callback($test);
    
    expect($test->matches($params))->toBe($expect);
})->with([
    [function($signature)
    {
        $signature->addParameter('integer');
    },[10],true
    ],
    [function($signature)
    {
        $signature->addParameter('integer');
        $signature->addParameter('string');
    },[10,'ABC'],true
    ],
    [function($signature)
    {
        $signature->addParameter('integer|string');
    },[10],true
    ],
    [function($signature)
    {
        $signature->addParameter('integer|string');
    },['abc'],true
    ], 
    [function($signature)
    {
        $signature->addParameter('integer');
        $signature->addParameter('string');
    },['ABC',10],false
    ],
    [function($signature)
    {
        $signature->addParameter('integer');
        $signature->addParameter('string');
    },[10],false
    ],
    [function($signature)
    {
        $signature->addParameter('integer');
        $signature->addParameter('string');
    },[10,'ABC',20],false
    ],
    ]);