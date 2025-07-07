<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Parser\Nodes\FloatNode;
use Sunhill\Parser\Nodes\StringNode;
use Sunhill\Parser\Nodes\DateNode;
use Sunhill\Parser\Nodes\DateTimeNode;
use Sunhill\Parser\Nodes\TimeNode;
use Sunhill\Parser\Nodes\BooleanNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Parser\Exceptions\AnalyzerException;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Tests\TestSupport\Parser\DummyAbstractAnalyzer;

uses(SunhillTestCase::class);

test('getTypeOfNode() works', function($input, $expect)
{
    $test = new DummyAbstractAnalyzer();
    $result = true;
    try {
        $test->analyze($input());
    } catch (AnalyzerException $e) {
        $result = false;    
    }
    expect($result)->toBe($expect);
})->with(
    [
        'integer'=>[function() { return new IntegerNode(1); },true],
        'float'=>[function() { return new FloatNode(1.1); },false],
        'string'=>[function() { return new StringNode("abc"); },false],
        'date'=>[function() { return new DateNode("2025-06-20"); },false],
        'datetime'=>[function() { return new DateTimeNode("2025-06-20 11:12:13"); },false],
        'time'=>[function() { return new TimeNode("11:12:13"); },false],    
        'boolean'=>[function() { return new BooleanNode(true); },true],
        'integer identifier'=>[function() { return new IdentifierNode('int_id'); },true],
        'unknown identifier'=>[function() { return new IdentifierNode('unknown'); },false],
        'integer < integer'=>[
            function() 
            {
                $result = new BinaryNode('<');
                $result->left(new IntegerNode(1));
                $result->right(new IntegerNode(2));
                return $result;
        },true],
        'float < integer'=>[
            function()
            {
                $result = new BinaryNode('<');
                $result->left(new FloatNode(1.2));
                $result->right(new IntegerNode(2));
                return $result;
        },true],
        'integer < float'=>[
            function()
            {
                $result = new BinaryNode('<');
                $result->right(new FloatNode(1.2));
                $result->left(new IntegerNode(2));
                return $result;
        },true],
        'string < float'=>[
            function()
            {
                $result = new BinaryNode('<');
                $result->right(new FloatNode(1.2));
                $result->left(new StringNode('abc'));
                return $result;
        },false],
        'boolean && boolean'=>[
            function()
            {
                $result = new BinaryNode('&&');
                $result->right(new BooleanNode(true));
                $result->left(new BooleanNode(true));
                return $result;
        },true],
        'integer && boolean'=>[
            function()
            {
                $result = new BinaryNode('&&');
                $result->right(new IntegerNode(1));
                $result->left(new BooleanNode(true));
                return $result;
        },true],
        'boolean && integer'=>[
            function()
            {
                $result = new BinaryNode('&&');
                $result->right(new BooleanNode(true));
                $result->left(new IntegerNode(1));
                return $result;
        },true],
        ]);