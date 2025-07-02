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
use Sunhill\Parser\Nodes\DateNode;
use Sunhill\Parser\Nodes\DateTimeNode;
use Sunhill\Parser\Nodes\TimeNode;

uses(SunhillTestCase::class);

test('BooleanNode (true)', function()
{
   $test = new BooleanNode(true);
   expect($test->getValue())->toBe(true);
   expect($test->getDatatype())->toBe('boolean');
   expect($test->toString())->toBe('true');
});

test('BooleanNode (false)', function()
{
    $test = new BooleanNode(false);
    expect($test->getValue())->toBe(false);
    expect($test->getDatatype())->toBe('boolean');
    expect($test->toString())->toBe('false');
});

test('IntegerNode', function()
{
    $test = new IntegerNode(123);
    expect($test->getValue())->toBe(123);
    expect($test->getDatatype())->toBe('integer');    
});

test('FloatNode', function()
{
    $test = new FloatNode(1.23);
    expect($test->getValue())->toBe(1.23);
    expect($test->getDatatype())->toBe('float');
});

test('StringNode', function()
{
    $test = new StringNode('abc');
    expect($test->getValue())->toBe('abc');
    expect($test->getDatatype())->toBe('string');    
});

test('IdentifierNode', function()
{
    $test = new IdentifierNode('testidentifier');
    expect($test->getName())->toBe('testidentifier');
    expect($test->getDatatype())->toBe(null);    
});

test('Array node (0 elements', function()
{
    $test = new ArrayNode(null);
    expect($test->elementCount())->toBe(0);
    expect($test->getDatatype())->toBe('array');
    expect($test->getDataSubtype())->toBe('empty');    
});

test('Array node (1 Element)', function()
{
    $test = new ArrayNode(new IntegerNode(10));
    
    expect($test->elementCount())->toBe(1);
    expect($test->getElement(0)->getValue())->toBe(10);
    expect($test->getDatatype())->toBe('array');
    expect($test->getDataSubtype())->toBe('integer');
});

test('Array node (2 Element)', function()
{
    $test = new ArrayNode(new IntegerNode(10));
    $test->addElement(new IntegerNode(20));
    expect($test->elementCount())->toBe(2);
    expect($test->getElement(0)->getValue())->toBe(10);
    expect($test->getElement(1)->getValue())->toBe(20);
});

test('Array getDataSubtype()', function($first, $second, $expect)
{
    $test = new ArrayNode($first());
    $test->addElement($second());
    expect($test->getDataSubtype())->toBe($expect);
})->with(
    [
        [function() { return new BooleanNode(true); },function() { return new BooleanNode(false); },'boolean'],
        [function() { return new DateNode('2025-06-27'); },function() { return new DateNode('2025-06-27'); },'date'],
        [function() { return new DateTimeNode('2025-06-27 10:11:12'); },function() { return new DateTimeNode('2025-06-27 10:11:12'); },'datetime'],
        [function() { return new FloatNode(10.2); },function() { return new FloatNode(0.1); },'float'],
        [function() { return new IntegerNode(10); },function() { return new IntegerNode(10); },'integer'],
        [function() { return new StringNode('abc'); },function() { return new StringNode('def'); },'string'],
        [function() { return new TimeNode('10:11:12'); },function() { return new TimeNode('10:11:12'); },'time'],

        [function() { return new IntegerNode(10); },function() { return new FloatNode(10.2); },'float'],
        [function() { return new DateNode('2025-06-27'); },function() { return new DateTimeNode('2025-06-27 11:22:23'); },'datetime'],
        [function() { return new IntegerNode(12); },function() { return new StringNode('abc'); },'mixed'],
        [function() { return new IntegerNode(12); },function() { return new DateNode('2025-06-27'); },'mixed'],
        [function() { return new IntegerNode(12); },function() { return new FunctionNode('abc'); },null],
        [function() { return new IntegerNode(12); },function() 
        { 
            $result = new FunctionNode('abc');
            $result->setDatatype('float');
            return $result;
        },'float'],
        [function() { return new IntegerNode(12); },function() { return new IdentifierNode('abc'); },null],
        [function() { return new IntegerNode(12); },function()
        {
            $result = new IdentifierNode('abc');
            $result->setDatatype('float');
            return $result;
        },'float'],
        ]);
test('UnaryNode', function()
{
    $test = new UnaryNode('+');
    $test->child(new IntegerNode(10));
    
    expect($test->getType())->toBe('+');
    expect($test->child()->getValue())->toBe(10);
    expect($test->getDatatype())->toBe('integer');
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

test('BinaryNode getDataSubtype()', function($first, $second, $expect)
{
    $test = new BinaryNode('+');
    $test->left($first());
    $test->right($second());
    expect($test->getDatatype())->toBe($expect);
})->with(
    [
        [function() { return new BooleanNode(true); },function() { return new BooleanNode(false); },'boolean'],
        [function() { return new DateNode('2025-06-27'); },function() { return new DateNode('2025-06-27'); },'date'],
        [function() { return new DateTimeNode('2025-06-27 10:11:12'); },function() { return new DateTimeNode('2025-06-27 10:11:12'); },'datetime'],
        [function() { return new FloatNode(10.2); },function() { return new FloatNode(0.1); },'float'],
        [function() { return new IntegerNode(10); },function() { return new IntegerNode(10); },'integer'],
        [function() { return new StringNode('abc'); },function() { return new StringNode('def'); },'string'],
        [function() { return new TimeNode('10:11:12'); },function() { return new TimeNode('10:11:12'); },'time'],
        
        [function() { return new IntegerNode(10); },function() { return new FloatNode(10.2); },'float'],
        [function() { return new DateNode('2025-06-27'); },function() { return new DateTimeNode('2025-06-27 11:22:23'); },'datetime'],
        [function() { return new IntegerNode(12); },function() { return new StringNode('abc'); },'mixed'],
        [function() { return new IntegerNode(12); },function() { return new DateNode('2025-06-27'); },'mixed'],
        [function() { return new IntegerNode(12); },function() { return new FunctionNode('abc'); },null],
        [function() { return new IntegerNode(12); },function()
        {
            $result = new FunctionNode('abc');
            $result->setDatatype('float');
            return $result;
        },'float'],
        [function() { return new IntegerNode(12); },function() { return new IdentifierNode('abc'); },null],
        [function() { return new IntegerNode(12); },function()
        {
            $result = new IdentifierNode('abc');
            $result->setDatatype('float');
            return $result;
        },'float'],
        ]);

test('FunctionNode with no arguments', function()
{
    $test = new FunctionNode('testfunc');
    expect($test->name())->toBe('testfunc');
    expect($test->getArgumentCount())->toBe(0);    
});

test('FunctionNode with single node', function()
{
    $test = new FunctionNode('testfunc');
    $test->arguments(new IntegerNode(10));
    expect($test->getArgumentCount())->toBe(1);
    expect($test->getArgument(0)->getValue())->toBe(10);
});

test('FunctionNode with array node', function()
{
    $arguments = new ArrayNode(new IntegerNode(10));
    $arguments->addElement(new StringNode('abc'));
    
    $test = new FunctionNode('testfunc');
    $test->arguments($arguments);
    expect($test->getArgumentCount())->toBe(2);
});

