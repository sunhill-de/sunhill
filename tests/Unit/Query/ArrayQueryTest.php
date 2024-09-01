<?php

use Sunhill\Query\ArrayQuery;
use Sunhill\Query\BasicQuery;
use Sunhill\Query\ConditionBuilder;
use Sunhill\Tests\Unit\Query\TestArrayQuery;
use Sunhill\Tests\TestCase;

uses(TestCase::class);

function assertDataEquals($assertion, $data)
{
    foreach ($assertion as $key => $value) {
        if ($data->$key !== $value) {
            expect(false)->toBe(true); //, $data->$key." is not asserted ".$value);
        }
    }
    expect(true)->toBe(true);
}

function assertArrayEquals($assertion, $data)
{
    $data = array_values($data->toArray());
    if (count($assertion) !== count($data)) {
        expect(false)->toBe(true);
        return;
    }
    for ($i=0;$i<count($assertion);$i++) {
        assertDataEquals($assertion[$i], $data[$i]);
    }
}


test('count works', function() {
    $test = new TestArrayQuery();
    
    expect($test->count())->toBe(3);
});

test('first works', function() 
{
    $test = new TestArrayQuery();
    
    assertDataEquals(['name'=>'ABC','value'=>123,'payload'=>'ZZZ'], $test->first());    
});

test('get works', function() 
{
    $test = new TestArrayQuery();
    assertArrayEquals([
        ['name'=>'ABC','value'=>123,'payload'=>'ZZZ'],
        ['name'=>'DEF','value'=>234,'payload'=>'XXX'],
        ['name'=>'GHI','value'=>345,'payload'=>'YYY'],
    ], $test->get());
});

test('get with order works', function()
{
    $test = new TestArrayQuery();
    
    assertArrayEquals([
        ['name'=>'DEF','value'=>234,'payload'=>'XXX'],
        ['name'=>'GHI','value'=>345,'payload'=>'YYY'],
        ['name'=>'ABC','value'=>123,'payload'=>'ZZZ'],
    ], $test->orderBy('payload')->get());
});

test('get with where works', function()
{
    $test = new TestArrayQuery();
            
    assertArrayEquals([
        ['name'=>'GHI','value'=>345,'payload'=>'YYY'],
    ], $test->where('name','GHI')->get());
});

test('get with more where works', function()
{
    $test = new TestArrayQuery();
    
    assertArrayEquals([
        ['name'=>'DEF','value'=>234,'payload'=>'XXX'],
    ], $test->where('name','>','ABC')->where('value','<',345)->get());
});

test('get with or where works', function()
{
    $test = new TestArrayQuery();
    
    assertArrayEquals([
        ['name'=>'ABC','value'=>123,'payload'=>'ZZZ'],
        ['name'=>'GHI','value'=>345,'payload'=>'YYY'],
    ], $test->where('name','=','ABC')->orWhere('value','=',345)->get());
});

test('get with or where as subquuery works', function()
{
    $test = new TestArrayQuery();
    
    assertArrayEquals([
        ['name'=>'ABC','value'=>123,'payload'=>'ZZZ'],
        ['name'=>'GHI','value'=>345,'payload'=>'YYY'],
    ], $test->where('name','=','ABC')->orWhere(function(ConditionBuilder $query) {
        $query->where('value','<',999)->where('name','>','DEF');
    })->get());
});

