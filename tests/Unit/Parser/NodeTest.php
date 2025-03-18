<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Parser\Nodes\BooleanNode;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Parser\Nodes\FloatNode;
use Sunhill\Parser\Nodes\StringNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Parser\Nodes\FunctionNode;
use Sunhill\Parser\Nodes\ArrayNode;
use Sunhill\Parser\Nodes\UnaryNode;
use Sunhill\Parser\Nodes\BinaryNode;

uses(SunhillTestCase::class);

test('BooleanNode', function()
{
   $test = new BooleanNode(true);
   expect($test->getValue())->toBe(true);
});

test('IntegerNode', function()
{
    $test = new IntegerNode(123);
    expect($test->getValue())->toBe(123);
    
});

test('FloatNode', function()
{
    $test = new FloatNode(1.23);
    expect($test->getValue())->toBe(1.23);
});

test('StringrNode', function()
{
    $test = new StringNode('abc');
    expect($test->getValue())->toBe('abc');
    
});

test('IdentifierNode', function()
{
    $test = new IdentifierNode('testidentifier');
    expect($test->getName())->toBe('testidentifier');
    
});

test('Array node (1 Element)', function()
{
    $test = new ArrayNode(new IntegerNode(10));
    
    expect($test->elementCount())->toBe(1);
    expect($test->getElement(0)->getValue())->toBe(10);
});

test('Array node (2 Element)', function()
{
    $test = new ArrayNode(new IntegerNode(10));
    $test->addElement(new IntegerNode(20));
    expect($test->elementCount())->toBe(2);
    expect($test->getElement(0)->getValue())->toBe(10);
    expect($test->getElement(1)->getValue())->toBe(20);
});

test('UnaryNode', function()
{
    $test = new UnaryNode('+');
    $test->child(new IntegerNode(10));
    
    expect($test->getType())->toBe('+');
    expect($test->child()->getValue())->toBe(10);
});

test('BinaryNode', function()
{
    $test = new BinaryNode('+');
    $test->left(new IntegerNode(10));
    $test->right(new IntegerNode(20));
    
    expect($test->getType())->toBe('+');
    expect($test->left()->getValue())->toBe(10);
    expect($test->right()->getValue())->toBe(20);
});

test('FunctionNode with no arguments', function()
{
    $test = new FunctionNode();
    $test->name('testfunc');
    expect($test->name())->toBe('testfunc');
    expect($test->getArgumentCount())->toBe(0);    
});

test('FunctionNode with single node', function()
{
    $test = new FunctionNode();
    $test->name('testfunc')->arguments(new IntegerNode(10));
    expect($test->getArgumentCount())->toBe(1);
    expect($test->getArgument(0)->getValue())->toBe(10);
});

test('FunctionNode with array node', function()
{
    $arguments = new ArrayNode(new IntegerNode(10));
    $arguments->addElement(new StringNode('abc'));
    
    $test = new FunctionNode();
    $test->name('testfunc')->arguments($arguments);
    expect($test->getArgumentCount())->toBe(2);
});

