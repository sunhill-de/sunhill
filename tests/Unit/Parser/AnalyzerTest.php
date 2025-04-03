<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Tests\Unit\Parser\Examples\DummyAnalyzer;
use Sunhill\Parser\Nodes\FloatNode;
use Sunhill\Parser\Nodes\BooleanNode;
use Sunhill\Parser\Nodes\StringNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Parser\Nodes\FunctionNode;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Parser\Nodes\UnaryNode;
use Sunhill\Parser\Nodes\ArrayNode;

uses(SunhillTestCase::class);

test('getType', function($tree, $expected)
{
    $test = new DummyAnalyzer($tree());
    expect(callProtectedMethod($test, 'getTypeOfNode', [$tree()]))->toBe($expected);
})->with([
    'simple integer node'=>[ function() { return new IntegerNode(10); }, 'integer'],
    'simple float node'=>[ function() { return new FloatNode(1.23); }, 'float'],
    'simple boolean node'=>[ function() { return new BooleanNode(true); }, 'boolean'],
    'simple string node'=>[ function() { return new StringNode('abc'); }, 'string'],
    'array node'=>[ function() { 
       $result = new ArrayNode(new StringNode('abc'));
       $result->addElement(new StringNode('def'));
       return $result;
    }, 'array'],
    'identifier node'=>[ function() { return new IdentifierNode('test_int'); }, 'integer'],
    'function node'=>[ function() { return new FunctionNode('sin'); }, 'float'],
    'binary tree node'=>[ function() 
    { 
        $result = new BinaryNode('+');
        $result->left(new IntegerNode(10));
        $result->right(new IntegerNode(20));
        return $result; 
    }, 'integer'],
    'invalid binary tree node'=>[ function()
    {
        $result = new BinaryNode('+');
        $result->left(new StringNode('abc'));
        $result->right(new IntegerNode(20));
        return $result;
    }, 'invalid'],
    'unary tree node'=>[ function()
    {
        $result = new UnaryNode('-');
        $result->child(new IntegerNode(10));
        return $result;
    }, 'integer'],
    'invalid unary tree node'=>[ function()
    {
        $result = new UnaryNode('+');
        $result->child(new IntegerNode(10));
        return $result;
    }, 'invalid'],
    ]);

test('analyze', function($tree, $expect, $expected_result) 
{
   $test = new DummyAnalyzer($tree());
   $test->addAcceptedType($expect);
   $result = 'success';
   try {
       $test->analyze($tree());
   } catch (\Sunhill\Parser\Exceptions\TypesMismatchException $e) {
       $result = 'type';
   } catch (\Sunhill\Parser\Exceptions\FunctionNotFoundException $e) {
       $result = 'function';
   } catch (\Sunhill\Parser\Exceptions\IdentifierNotFoundException $e) {
       $result = 'identifier';
   } catch (\Sunhill\Parser\Exceptions\TypeNotExpectedException $e) {
       $result = 'resulttype';
   } catch (\Sunhill\Parser\Exceptions\FunctionParameterException $e) {
       $result = 'parameters';
   }
   expect($result)->toBe($expected_result);
})->with([
    'simple integer node'=>[ function() { return new IntegerNode(10); }, 'integer', 'success'],
    'simple float node'=>[ function() { return new FloatNode(1.23); }, 'float', 'success'],
    'simple boolean node'=>[ function() { return new BooleanNode(true); }, 'boolean', 'success'],
    'simple string node'=>[ function() { return new StringNode('abc'); }, 'string', 'success'],
    'unexpected integer node'=>[ function() { return new IntegerNode(10); }, 'string', 'resulttype'],
    'array node'=>[ function() 
        {
            $result = new ArrayNode(new StringNode('abc'));
            $result->addElement(new StringNode('def'));
            return $result;
        }, 'array', 'success'],
    'identifier node'=>[ function() 
        { 
            return new IdentifierNode('test_int'); 
        }, 'integer', 'success'],
    'unknown identifier node'=>[ function()
        {
            return new IdentifierNode('unknown');
        }, 'integer', 'identifier'],
    'function node'=>[ function() 
        { 
            $result = new FunctionNode('sin');
            $result->arguments(new FloatNode('3.14'));
            return $result;
        }, 'float', 'success'],
     'function node with optional parameter given'=>[ function()
        {
            $result = new FunctionNode('sin');
            $result->arguments(new StringNode('ABC'));
            return $result;
        }, 'string', 'success'],
     'function node with optional parameter omitted'=>[ function()
        {
            $result = new FunctionNode('sin');
            return $result;
        }, 'string', 'success'],
     'function node with no parameters'=>[ function()
        {
            $result = new FunctionNode('time');
            return $result;
        }, 'integer', 'success'],
    'function node with to less parameters'=>[ function()
        {
            $result = new FunctionNode('sin');
            return $result;
        }, 'float', 'parameters'],
    'function node with type mismatch'=>[ function()
        {
            $result = new FunctionNode('sin');
            $result->arguments(new StringNode('ABC'));
            return $result;
        }, 'float', 'parameters'],
    'function node with too many parameters'=>[ function()
        {
            $result = new FunctionNode('sin');
            $params = new ArrayNode(new FloatNode('3.14'));
            $params->addElement(new FloatNode('3.14'));
            $result->arguments($params);
            return $result;
        }, 'float', 'parameters'],
     'unknown function node'=>[ function()
        {
            $result = new FunctionNode('unknown');
            $result->arguments(new FloatNode('3.14'));
            return $result;
        }, 'float', 'function'],
     'ellipsis function node'=>[ function()
        {
            $result = new FunctionNode('test_ellipsis');
            $params = new ArrayNode(new StringNode('ABC'));
            $params->addElement(new StringNode('DEF'));
            $params->addElement(new StringNode('GHI'));
            $result->arguments($params);
            return $result;
        }, 'string', 'success'],
     'ellipsis function node with too few parameter'=>[ function()
        {
            $result = new FunctionNode('test_ellipsis');
            $params = new ArrayNode(new StringNode('ABC'));
            $result->arguments($params);
            return $result;
        }, 'string', 'parameters'],
     'mixed ellipsis function node'=>[ function()
        {
            $result = new FunctionNode('test_ellipsis');
            $params = new ArrayNode(new IntegerNode(10));
            $params->addElement(new StringNode('ABC'));
            $params->addElement(new StringNode('DEF'));
            $params->addElement(new StringNode('GHI'));
            $params->addElement(new StringNode('JKL'));
            $result->arguments($params);
            return $result;
        }, 'string', 'success'],
     'mixed ellipsis function node with too few parameter'=>[ function()
        {
            $result = new FunctionNode('test_mixedellipsis');
            $params = new ArrayNode(new IntegerNode(10));
            $params->addElement(new StringNode('ABC'));
            $result->arguments($params);
            return $result;
        }, 'string', 'parameters'],
     'binary noode'=>[ function()
        {
            $result = new BinaryNode('+');
            $result->left(new IntegerNode(10));
            $result->right(new IntegerNode(20));
            return $result;
        }, 'integer', 'success'],
     'binary node with mixed tree'=>[ function()
        {
            $result = new BinaryNode('+');
            $result->left(new IntegerNode(10));
            $result->right(new FloatNode(3.14));
            return $result;
        }, 'float', 'success'],
    'binary node with wrong types'=>[ function()
        {
            $result = new BinaryNode('+');
            $result->left(new StringNode('ABC'));
            $result->right(new FloatNode(3.14));
            return $result;
        }, 'string', 'type'],
        
    ]);