<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\Query;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Tests\Unit\Parser\Examples\DummyExecutor;

uses(SunhillTestCase::class);

test('Empty query', function()
{
    $test = new Query();
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
        ->toBe('where:[],order:[],group:[],offset:[],limit:[]');    
});

// ================================ offset ========================================
test('Offset: Just a simple integer', function()
{
    $test = new Query();
    $test->offset(5);
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[],group:[],offset:[5],limit:[]');
});

test('Offset: A callback', function()
{
    $test = new Query();
    $test->offset(function() { return 5; });
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[],group:[],offset:[5],limit:[]');
});

test('Offset: An expression', function()
{
    $test = new Query();
    $test->offset("5+3");
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[],group:[],offset:[(5)+(3)],limit:[]');
});

test('Offset: An string expression', function()
{
    $test = new Query();
    $test->offset("'5'");
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[],group:[],offset:[5],limit:[]');
});

test('Offset: A node', function()
{
    $test = new Query();
    $test->offset(new IntegerNode(5));
    
    $ast = $test->getQueryNode();
    expect($ast->offset()->getType())->toBe('integer');
    expect($ast->offset()->getValue())->toBe(5);
});

// ================================ limit ========================================
test('Limit: Just a simple integer', function()
{
    $test = new Query();
    $test->limit(5);
    
    $ast = $test->getQueryNode();
    expect($ast->limit()->getType())->toBe('integer');
    expect($ast->limit()->getValue())->toBe(5);
});

test('Limit: A callback', function()
{
    $test = new Query();
    $test->limit(function() { return 5; });
    
    $ast = $test->getQueryNode();
    expect($ast->limit()->getType())->toBe('integer');
    expect($ast->limit()->getValue())->toBe(5);
});

test('Limit: An expression', function()
{
    $test = new Query();
    $test->limit("5+3");
    
    $ast = $test->getQueryNode();
    expect($ast->limit()->getType())->toBe('+');
    expect($ast->limit()->left()->getType())->toBe('integer');
    expect($ast->limit()->right()->getType())->toBe('integer');
});

test('Limit: An string expression', function()
{
    $test = new Query();
    $test->limit("'5'");
    
    $ast = $test->getQueryNode();
    expect($ast->limit()->getType())->toBe('integer');
    expect($ast->limit()->getValue())->toBe(5);
});

test('Limit: A node', function()
{
    $test = new Query();
    $test->limit(new IntegerNode(5));
    
    $ast = $test->getQueryNode();
    expect($ast->limit()->getType())->toBe('integer');
    expect($ast->limit()->getValue())->toBe(5);
});

// ============================ Order ===================================
test('Order: Just two strings', function()
{
    $test = new Query();
    $test->order("a","ASC");
    
    $ast = $test->getQueryNode();
    expect($ast->order()->getType())->toBe('order');
    expect($ast->order()->field())->toBe('a');
    expect($ast->order()->direction())->toBe('asc');
});

test('Order: Just a string (direction omitted)', function()
{
    $test = new Query();
    $test->order("a");
    
    $ast = $test->getQueryNode();
    expect($ast->order()->getType())->toBe('order');
    expect($ast->order()->field())->toBe('a');
    expect($ast->order()->direction())->toBe('asc');
});

test('Order: Callback returning a string with direction', function()
{
    $test = new Query();
    $test->order(function() { return "a desc"; });
    
    $ast = $test->getQueryNode();
    expect($ast->order()->getType())->toBe('order');
    expect($ast->order()->field())->toBe('a');
    expect($ast->order()->direction())->toBe('desc');
});

test('Order: callback returning a string without direction', function()
{
    $test = new Query();
    $test->order(function() { return "a"; });
    
    $ast = $test->getQueryNode();
    expect($ast->order()->getType())->toBe('order');
    expect($ast->order()->field())->toBe('a');
    expect($ast->order()->direction())->toBe('asc');
});

test('Order: callback returning a stdclass with direction', function()
{
    $test = new Query();
    $test->order(function() { $return = new \stdClass(); $return->field = 'a'; $return->direction = 'desc'; });
    
    $ast = $test->getQueryNode();
    expect($ast->order()->getType())->toBe('order');
    expect($ast->order()->field())->toBe('a');
    expect($ast->order()->direction())->toBe('desc');
});

test('Order: callback returning a stdclass without direction', function()
{
    $test = new Query();
    $test->order(function() { $return = new \stdClass(); $return->field = 'a'; });
    
    $ast = $test->getQueryNode();
    expect($ast->order()->getType())->toBe('order');
    expect($ast->order()->field())->toBe('a');
    expect($ast->order()->direction())->toBe('asc');
});

// ================================ fields ===================================
