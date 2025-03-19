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

uses(SunhillTestCase::class);

test('getType', function($tree, $expected)
{
    $test = new DummyAnalyzer($tree());
    expect($test->getType())->toBe($expected);
})->with([
    'simple integer node'=>[ function() { return new IntegerNode(10); }, 'integer'],
    'simple float node'=>[ function() { return new FloatNode(1.23); }, 'float'],
    'simple boolean node'=>[ function() { return new BooleanNode(true); }, 'boolean'],
    'simple string node'=>[ function() { return new StringNode('abc'); }, 'string'],
    'identifier node'=>[ function() { return new IdentifierNode('test_int'); }, 'integer'],
    'function node'=>[ function() { return new FunctionNode('sin'); }, 'float'],
    'binary tree node'=>[ function() 
    { 
        $result = new BinaryNode('+');
        $result->left(new IntegerNode(10));
        $result->right(new IntegerNode(20));
        return $result; 
    }, 'integer'],
    ]);