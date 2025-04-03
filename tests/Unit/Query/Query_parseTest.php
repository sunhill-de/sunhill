<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\Query;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Tests\Unit\Parser\Examples\DummyExecutor;
use Sunhill\Facades\Queries;
use Sunhill\Parser\Nodes\StringNode;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Query\QueryParser\OrderNode;
use Sunhill\Query\Exceptions\InvalidOrderException;

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
    $node = new BinaryNode('+');
    $node->left(new IntegerNode(5));
    $node->right(new IntegerNode(3));
    Queries::shouldReceive('parseQueryString')->with("5+3")->once()->andReturn($node);
    $test = new Query();
    $test->offset("5+3");
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[],group:[],offset:[(5)+(3)],limit:[]');
});

test('Offset: An string expression', function()
{
    Queries::shouldReceive('parseQueryString')->with("'5'")->once()->andReturn(new StringNode("5"));
    $test = new Query();
    $test->offset("'5'");
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[],group:[],offset:["5"],limit:[]');
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
test('limit: Just a simple integer', function()
{
    $test = new Query();
    $test->limit(5);
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[],group:[],offset:[],limit:[5]');
});

test('limit: A callback', function()
{
    $test = new Query();
    $test->limit(function() { return 5; });
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[],group:[],offset:[],limit:[5]');
});

test('limit: An expression', function()
{
    $node = new BinaryNode('+');
    $node->left(new IntegerNode(5));
    $node->right(new IntegerNode(3));
    Queries::shouldReceive('parseQueryString')->with("5+3")->once()->andReturn($node);
    $test = new Query();
    $test->limit("5+3");
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[],group:[],offset:[],limit:[(5)+(3)]');
});

test('limit: An string expression', function()
{
    Queries::shouldReceive('parseQueryString')->with("'5'")->once()->andReturn(new StringNode("5"));
    $test = new Query();
    $test->limit("'5'");
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[],group:[],offset:[],limit:["5"]');
});

test('limit: A node', function()
{
    $test = new Query();
    $test->limit(new IntegerNode(5));
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[],group:[],offset:[],limit:[5]');
});

// ============================ Order ===================================
test('Order: Just two strings', function()
{
    Queries::shouldReceive('parseQueryString')->with("a")->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->order("a","ASC");
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
        ->toBe('where:[],order:[a asc],group:[],offset:[],limit:[]');    
});

test('Order: Just a string (direction omitted)', function()
{
    Queries::shouldReceive('parseQueryString')->with("a")->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->order("a");
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[a asc],group:[],offset:[],limit:[]');
});

test('Order: Just a string with order statement', function()
{
    $result = new OrderNode();
    $result->field(new IdentifierNode('a'));
    $result->direction('desc');
    Queries::shouldReceive('parseQueryString')->with("a desc")->once()->andReturn($result);
    
    $test = new Query();
    $test->order("a desc");
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[a desc],group:[],offset:[],limit:[]');
});

test('Order: stdclass with direction', function()
{
    Queries::shouldReceive('parseQueryString')->with("a")->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $return = new \stdClass(); 
    $return->field = 'a'; 
    $return->direction = 'desc';
    $test->order($return);
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[a desc],group:[],offset:[],limit:[]');
});

test('Order: stdclass without direction', function()
{
    Queries::shouldReceive('parseQueryString')->with("a")->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $return = new \stdClass(); 
    $return->field = 'a';
    $test->order($return);
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[a asc],group:[],offset:[],limit:[]');
});

test('Order: Callback returning a string with direction', function()
{
    $result = new OrderNode();
    $result->field(new IdentifierNode('a'));
    $result->direction('desc');
    Queries::shouldReceive('parseQueryString')->with("a desc")->once()->andReturn($result);
    
    $test = new Query();
    $test->order(function() { return "a desc"; });
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[a desc],group:[],offset:[],limit:[]');
});

test('Order: callback returning a string without direction', function()
{
    Queries::shouldReceive('parseQueryString')->with("a")->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->order(function() { return "a"; });
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[a asc],group:[],offset:[],limit:[]');
});

test('Order: callback returning a stdclass with direction', function()
{
    Queries::shouldReceive('parseQueryString')->with("a")->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->order(function() 
    { 
        $return = new \stdClass(); 
        $return->field = 'a'; 
        $return->direction = 'desc';
        
        return $return;
    });
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[a desc],group:[],offset:[],limit:[]');
});

test('Order: callback returning a stdclass without direction', function()
{
    Queries::shouldReceive('parseQueryString')->with("a")->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->order(function() 
    { 
        $return = new \stdClass(); 
        $return->field = 'a';
        
        return $return;
    });
    
    $executor = new DummyExecutor();
    expect($executor->execute($test->getQueryNode()))
    ->toBe('where:[],order:[a asc],group:[],offset:[],limit:[]');
});

test('Order: it fails when invalid direction is given', function()
{
    Queries::shouldReceive('parseQueryString')->with("a")->once()->andReturn(new IdentifierNode('a'));
    
    $test = new Query();
    $test->order('a','invalid');    
})->throws(InvalidOrderException::class);


// ================================ fields ===================================
