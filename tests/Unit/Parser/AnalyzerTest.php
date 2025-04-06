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

test('analyze', function($tree, $expect, $expected_result) 
{
   $test = new DummyAnalyzer($tree());
   $test->addAcceptedType($expect);
   try {
       $result = $test->analyze($tree());
   } catch (\Sunhill\Parser\Exceptions\TypesMismatchException $e) {
       $result = 'type';
   } catch (\Sunhill\Parser\Exceptions\TypeMismatchException $e) {
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
    'simple integer node'=>[ function() { return new IntegerNode(10); }, 'integer', 'integer'],
    'simple float node'=>[ function() { return new FloatNode(1.23); }, 'float', 'float'],
    'simple boolean node'=>[ function() { return new BooleanNode(true); }, 'boolean', 'boolean'],
    'simple string node'=>[ function() { return new StringNode('abc'); }, 'string', 'string'],
    'unexpected integer node'=>[ function() { return new IntegerNode(10); }, 'string', 'resulttype'],
    'array node'=>[ function() 
        {
            $result = new ArrayNode(new StringNode('abc'));
            $result->addElement(new StringNode('def'));
            return $result;
        }, 'array', 'array'],
    'identifier node'=>[ function() 
        { 
            return new IdentifierNode('test_int'); 
        }, 'integer', 'integer'],
    'unknown identifier node'=>[ function()
        {
            return new IdentifierNode('unknown');
        }, 'integer', 'identifier'],
    'function node'=>[ function() 
        { 
            $result = new FunctionNode('sin');
            $result->arguments(new FloatNode('3.14'));
            return $result;
        }, 'float', 'float'],
     'function node with optional parameter given'=>[ function()
        {
            $result = new FunctionNode('test_function');
            $result->arguments(new StringNode('ABC'));
            return $result;
        }, 'string', 'string'],
     'function node with optional parameter omitted'=>[ function()
        {
            $result = new FunctionNode('test_function');
            return $result;
        }, 'string', 'string'],
     'function node with no parameters'=>[ function()
        {
            $result = new FunctionNode('time');
            return $result;
        }, 'integer', 'integer'],
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
        }, 'string', 'string'],
     'ellipsis function node with too few parameter'=>[ function()
        {
            $result = new FunctionNode('test_ellipsis');
            $params = new ArrayNode(new StringNode('ABC'));
            $result->arguments($params);
            return $result;
        }, 'string', 'parameters'],
     'mixed ellipsis function node'=>[ function()
        {
            $result = new FunctionNode('test_mixedellipsis');
            $params = new ArrayNode(new IntegerNode(10));
            $params->addElement(new StringNode('ABC'));
            $params->addElement(new StringNode('DEF'));
            $params->addElement(new StringNode('GHI'));
            $params->addElement(new StringNode('JKL'));
            $result->arguments($params);
            return $result;
        }, 'string', 'string'],
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
        }, 'integer', 'integer'],
     'binary node with mixed tree'=>[ function()
        {
            $result = new BinaryNode('+');
            $result->left(new IntegerNode(10));
            $result->right(new FloatNode(3.14));
            return $result;
        }, 'float', 'float'],
    'binary node with wrong types'=>[ function()
        {
            $result = new BinaryNode('+');
            $result->left(new StringNode('ABC'));
            $result->right(new FloatNode(3.14));
            return $result;
        }, 'string', 'type'],
        
    ]);