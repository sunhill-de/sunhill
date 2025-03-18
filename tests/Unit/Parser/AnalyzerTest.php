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
    [ function() { return new IntegerNode(10); }, 'integer'],
    [ function() { return new FloatNode(1.23); }, 'float'],
    [ function() { return new BooleanNode(true); }, 'boolean'],
    [ function() { return new StringNode('abc'); }, 'string'],
    [ function() { return new IdentifierNode('test_int'); }, 'integer'],
    [ function() { return new FunctionNode('sin'); }, 'float'],
    [ function() 
    { 
        $result = new BinaryNode('+');
        $result->left(new IntegerNode(10));
        $result->right(new IntegerNode(20));
        return $result; 
    }, 'float'],
    ]);