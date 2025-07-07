<?php
/**
 * @file OrderNodeTest.php
 * tests: /src/Query/QueryParser/OrderNode.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Query\QueryParser\Nodes\OrderNode;
use Sunhill\Query\Exceptions\InvalidOrderException;

uses(SunhillTestCase::class);

test('constructor works', function()
{
   $test = new OrderNode(); 
   expect($test->direction())->toBe('asc');
});

test('constructor works with passed parameters', function()
{
    $expression = \Mockery::mock(IdentifierNode::class);
    $expression->shouldReceive('getName')->once()->andReturn('str_id');
    $test = new OrderNode($expression, 'desc');
    expect($test->field()->getName())->toBe('str_id');
    expect($test->direction())->toBe('desc');
});

test('setter works', function()
{
    $expression = \Mockery::mock(IdentifierNode::class);
    $expression->shouldReceive('getName')->once()->andReturn('str_id');
    $test = new OrderNode();
    $test->field($expression);
    $test->direction('desc');
    expect($test->field()->getName())->toBe('str_id');
    expect($test->direction())->toBe('desc');
});

test('setter trims', function()
{
    $expression = \Mockery::mock(IdentifierNode::class);
    $expression->shouldReceive('getName')->once()->andReturn('str_id');
    $test = new OrderNode();
    $test->field($expression);
    $test->direction(' desc ');
    expect($test->field()->getName())->toBe('str_id');
    expect($test->direction())->toBe('desc');
});

test('toString() works', function()
{
    $expression = \Mockery::mock(IdentifierNode::class);
    $expression->shouldReceive('toString')->once()->andReturn('str_id');
    $test = new OrderNode();
    $test->field($expression);
    expect($test->toString())->toBe('str_id ASC');
});

test('validate() works and passes', function()
{
    $expression = \Mockery::mock(IdentifierNode::class);
    $expression->shouldReceive('validate')->once();
    $test = new OrderNode();
    $test->field($expression);
    $test->validate();
});

test('validate() fails when field is something different', function()
{
    $expression = \Mockery::mock(IntegerNode::class);
    $expression->shouldReceive('validate')->once();
    $test = new OrderNode();
    $test->field($expression);
    $test->validate();
})->throws(InvalidOrderException::class);

test('validate() handles direction correctly', function($direction, $pass)
{
    $expression = \Mockery::mock(IdentifierNode::class);
    $expression->shouldReceive('validate')->once();
    $test = new OrderNode();
    $test->field($expression);
    $test->direction($direction);
    $result = true;
    try {
        $test->validate();
    } catch (\Sunhill\Query\Exceptions\InvalidOrderException $e) {
        $result = false;
    }
    expect($result)->toBe($pass);
})->with([
    ['asc', true],
    ['Asc', true],
    ['ASC', true],
    ['desc', true],
    ['random', false]
]);