<?php
/**
 * @file FunctionNodeTest.php
 * tests: /src/Parser/Nodes/FunctionNode.php
 * free of dependent units: yes
 */

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
use Sunhill\Parser\Exceptions\AnalyzerException;
use Sunhill\Parser\LanguageDescriptor\FunctionDescriptor;

uses(SunhillTestCase::class);

test('getDatatype()', function($modifier, $expect)
{
    expect($modifier()->getDatatype())->toBe($expect);
})->with(
[
    'IdentifierNode (type set)'=>[function() 
    { 
        $return = new IdentifierNode('test');
        $return->setDatatype('integer');
        return  $return;
    }, 'integer'],
    'IdentifierNode (type not set)'=>[function() { return new IdentifierNode('test'); }, null],
    ]);

test('toString()', function($modifier, $expect)
{
    expect($modifier()->toString())->toBe($expect);
})->with(
[
    'IdentifierNode (type set)'=>[function()
    {
        $return = new IdentifierNode('test');
        $return->setDatatype('integer');
        return  $return;
    }, 'test'],
    'IdentifierNode (type not set)'=>[function() { return new IdentifierNode('test'); }, 'test'],
    ]);

test('validate()', function($modifier, $expect)
{
    $result = true;
    try {
        $modifier()->validate();
    } catch (AnalyzerException $e) {
        $result = false;
    }
    expect($result)->toBe($expect);
})->with(
[
    'IdentifierNode (type set)'=>[function()
    {
        $return = new IdentifierNode('test');
        $return->setDatatype('integer');
        return  $return;
    }, true],
    'IdentifierNode (type not set)'=>[function() { return new IdentifierNode('test'); }, false],
    ]);

test('UnaryNode', function()
{
    $test = new UnaryNode('+');
    $test->child(new IntegerNode(10));
    
    expect($test->getType())->toBe('+');
    expect($test->child()->getValue())->toBe(10);
    expect($test->getDatatype())->toBe('integer');
});

