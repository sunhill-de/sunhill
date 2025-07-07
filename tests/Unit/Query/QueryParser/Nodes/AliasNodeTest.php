<?php
/**
 * @file AliasNodeTest.php
 * tests: /src/Query/QueryParser/AliasNode.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\QueryParser\Nodes\AliasNode;
use Sunhill\Parser\Nodes\IntegerNode;

uses(SunhillTestCase::class);

test('constructor works', function()
{
   $expression = \Mockery::mock(IntegerNode::class);
   $expression->shouldReceive('getValue')->once()->andReturn(10);
   $test = new AliasNode($expression, "integeralias"); 
   expect($test->expression()->getValue())->toBe(10);
   expect($test->alias())->toBe('integeralias');
});

test('setter works', function()
{
    $expression1 = \Mockery::mock(IntegerNode::class);
    $test = new AliasNode($expression1, "integeralias");
    $expression2 = \Mockery::mock(IntegerNode::class);
    $expression2->shouldReceive('getValue')->once()->andReturn(20);
    $test->expression($expression2);
    $test->alias('anotheralias');
    expect($test->expression()->getValue())->toBe(20);
    expect($test->alias())->toBe('anotheralias');
});

test('toString() works', function()
{
    $expression = \Mockery::mock(IntegerNode::class);
    $expression->shouldReceive('toString')->once()->andReturn('10');
    $test = new AliasNode($expression, "integeralias");
    expect($test->toString())->toBe('10 AS integeralias');
});

test('validate() works and passes', function()
{
    $expression = \Mockery::mock(IntegerNode::class);
    $expression->shouldReceive('validate')->once();
    $test = new AliasNode($expression, 'alias');
    $test->validate();
});

test('validate() handles alias correctly', function($alias, $pass)
{
    $expression = \Mockery::mock(IntegerNode::class);
    $expression->shouldReceive('validate')->once();
    $test = new AliasNode($expression, $alias);
    $result = true;
    try {
        $test->validate();
    } catch (\Sunhill\Query\Exceptions\InvalidAliasException $e) {
        $result = false;
    }
    expect($result)->toBe($pass);
})->with([
    ['abcABC0123', true],
    ['_abc', true],
    ['abc_def', true],
    ['0abc', false],
    ['ab!cd', false]
]);