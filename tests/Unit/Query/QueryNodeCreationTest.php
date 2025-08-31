<?php
/**
 * @file QueryNodeCreationTest.php
 * tests: /src/Query/Node.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Query\Query;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Parser\Nodes\FloatNode;
use Sunhill\Parser\Nodes\StringNode;
use Sunhill\Parser\Nodes\DateNode;
use Sunhill\Parser\Nodes\DateTimeNode;
use Sunhill\Parser\Nodes\TimeNode;
use Sunhill\Parser\Nodes\UnaryNode;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Parser\Nodes\BooleanNode;
use Sunhill\Parser\Nodes\FunctionNode;
use Sunhill\Parser\Nodes\ArrayNode;

uses(SunhillSimpleTestCase::class);

test('Create identifier node', function()
{
    $node = Query::identifier('test');
    
    expect(is_a($node, IdentifierNode::class))->toBe(true);
    expect($node->getName())->toBe('test');
    expect($node->toString())->toBe('test');
});

test('Create integer constant node', function()
{
   $node = Query::integerConstant(10);
   
   expect(is_a($node, IntegerNode::class))->toBe(true);
   expect($node->getValue())->toBe(10);
   expect($node->toString())->toBe('10');
});

test('Create float constant node', function()
{
    $node = Query::floatConstant(10.23);
    
    expect(is_a($node, FloatNode::class))->toBe(true);
    expect($node->getValue())->toBe(10.23);
    expect($node->toString())->toBe('10.23');
});

test('Create string constant node', function()
{
    $node = Query::stringConstant('abc');
    
    expect(is_a($node, StringNode::class))->toBe(true);
    expect($node->getValue())->toBe('abc');
    expect($node->toString())->toBe('"abc"');
});

test('Create boolean constant node', function()
{
    $node = Query::booleanConstant(true);
    
    expect(is_a($node, BooleanNode::class))->toBe(true);
    expect($node->getValue())->toBe(true);
    expect($node->toString())->toBe('true');    
});

test('Create date constant node', function()
{
    $node = Query::dateConstant('2025-06-29');
    
    expect(is_a($node, DateNode::class))->toBe(true);
    expect($node->getValue())->toBe('2025-06-29');
    expect($node->toString())->toBe('"2025-06-29"');
});

test('Create datetime constant node', function()
{
    $node = Query::datetimeConstant('2025-06-29 11:12:13');
    
    expect(is_a($node, DateTimeNode::class))->toBe(true);
    expect($node->getValue())->toBe('2025-06-29 11:12:13');
    expect($node->toString())->toBe('"2025-06-29 11:12:13"');
});

test('Create time constant node', function()
{
    $node = Query::timeConstant('11:12:13');
    
    expect(is_a($node, TimeNode::class))->toBe(true);
    expect($node->getValue())->toBe('11:12:13');
    expect($node->toString())->toBe('"11:12:13"');
});

test('Create function node without arguments', function()
{
    $node = Query::funct('somefunction');
    
    expect(is_a($node, FunctionNode::class))->toBe(true);
    expect($node->name())->toBe('somefunction');
    expect($node->toString())->toBe('somefunction()');
});

test('Create function node with one argument', function()
{
    $node = Query::funct('somefunction',Query::integerConstant(10));
    
    expect(is_a($node, FunctionNode::class))->toBe(true);
    expect($node->name())->toBe('somefunction');
    expect($node->toString())->toBe('somefunction(10)');
});

test('Create function node with two arguments passed as array', function()
{
    $node = Query::funct('somefunction',[Query::integerConstant(10),Query::stringConstant('abc')]);
    
    expect(is_a($node, FunctionNode::class))->toBe(true);
    expect($node->name())->toBe('somefunction');
    expect($node->toString())->toBe('somefunction(10,"abc")');
});

test('Create function node with two arguments passed as array node', function()
{
    $args = new ArrayNode(Query::integerConstant(10));
    $args->addElement(Query::stringConstant('abc'));
    $node = Query::funct('somefunction',$args);
    
    expect(is_a($node, FunctionNode::class))->toBe(true);
    expect($node->name())->toBe('somefunction');
    expect($node->toString())->toBe('somefunction(10,"abc")');
});

test('Create array node without elements', function()
{
    $node = Query::array();
    
    expect(is_a($node, ArrayNode::class))->toBe(true);
    expect($node->toString())->toBe('[]');
});

test('Create array node with one element', function()
{
    $node = Query::array(Query::integerConstant(10));
    
    expect(is_a($node, arrayNode::class))->toBe(true);
    expect($node->toString())->toBe('[10]');
});

test('Create array node with two elements', function()
{
    $node = Query::array([Query::integerConstant(10),Query::stringConstant('abc')]);
    
    expect(is_a($node, ArrayNode::class))->toBe(true);
    expect($node->toString())->toBe('[10,"abc"]');
});

test('Create unary operator node', function()
{
    $node = Query::unaryOperator('-', Query::integerConstant(10));
    
    expect(is_a($node, UnaryNode::class))->toBe(true);
    expect($node->getType())->toBe('-');
    expect($node->child()->getValue())->toBe(10);
    expect($node->toString())->toBe('(-10)');
});

test('Create binary operator node', function()
{
    $node = Query::binaryOperator('-', Query::integerConstant(10), Query::integerConstant(20));
    
    expect(is_a($node, BinaryNode::class))->toBe(true);
    expect($node->getType())->toBe('-');
    expect($node->left()->getValue())->toBe(10);
    expect($node->right()->getValue())->toBe(20);
    expect($node->toString())->toBe('(10-20)');
});

