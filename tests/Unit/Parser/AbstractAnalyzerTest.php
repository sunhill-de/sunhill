<?php

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Parser\Nodes\FloatNode;
use Sunhill\Parser\Nodes\StringNode;
use Sunhill\Parser\Nodes\DateNode;
use Sunhill\Parser\Nodes\DateTimeNode;
use Sunhill\Parser\Nodes\TimeNode;
use Sunhill\Tests\Unit\Parser\Examples\DummyAbstractAnalyzer;
use Sunhill\Parser\Nodes\BooleanNode;
use Sunhill\Parser\Nodes\IdentifierNode;

uses(SunhillTestCase::class);

test('getTypeOfNode() works', function($input, $expect)
{
    $test = new DummyAbstractAnalyzer();
    expect($test->getTypeOfNode($input()))->toBe($expect);
})->with(
    [
        'integer'=>[function() { return new IntegerNode(1); },'integer'],
        'float'=>[function() { return new FloatNode(1.1); },'float'],
        'string'=>[function() { return new StringNode("abc"); },'string'],
        'date'=>[function() { return new DateNode("2025-06-20"); },'date'],
        'datetime'=>[function() { return new DateTimeNode("2025-06-20 11:12:13"); },'datetime'],
        'time'=>[function() { return new TimeNode("11:12:13"); },'time'],    
        'boolean'=>[function() { return new BooleanNode(true); },'boolean'],
        'integer identifier'=>[function() { return new IdentifierNode('int_id'); },'integer'],
        'float identifier'=>[function() { return new IdentifierNode('float_id'); },'float'],
        'string identifier'=>[function() { return new IdentifierNode('string_id'); },'string'],
        ]);