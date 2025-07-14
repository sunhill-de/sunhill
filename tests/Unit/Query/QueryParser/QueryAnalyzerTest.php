<?php
/**
 * @file QueryAnalyzerTest.php
 * tests: /src/Query/QueryParser/QueryAnalyzer.php
 * free of dependent units: 
 */

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Properties\RecordProperty;
use Sunhill\Query\QueryParser\QueryAnalyzer;
use Sunhill\Properties\AbstractProperty;
use Sunhill\Parser\Nodes\BooleanNode;
use Sunhill\Parser\Nodes\DateNode;
use Sunhill\Parser\Nodes\DateTimeNode;
use Sunhill\Parser\Nodes\FloatNode;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Parser\Nodes\StringNode;
use Sunhill\Parser\Nodes\TimeNode;

uses(SunhillTestCase::class);

test('analyze identifier', function()
{
    $node = \Mockery::mock(IdentifierNode::class);
    $node->shouldReceive('getName')->andReturn('test');
    $node->shouldReceive('setDatatype')->once()->with('integer');
    $node->shouldReceive('getDatatype')->andReturn('integer');
    $node->shouldReceive('validate')->once();
    $property = \Mockery::mock(AbstractProperty::class);
    $property->shouldReceive('getAccessType')->once()->andReturn('integer');
    $record = \Mockery::mock(RecordProperty::class);
    $record->shouldReceive('getElement')->once()->with('test')->andReturn($property);
    
    $test = new QueryAnalyzer($record);
    $test->analyze($node);
});

test('analyze constants', function($class, $type)
{
    $record = \Mockery::mock(RecordProperty::class);
    $test = new QueryAnalyzer($record);
    $node = \Mockery::mock($class);
    $node->shouldReceive('getDatatype')->once()->andReturn($type);
    $node->shouldReceive('validate')->once();
    $test->analyze($node);
})->with([
        'boolean'=>[BooleanNode::class,'boolean'],
        'date'=>[DateNode::class,'date'],
        'datetime'=>[DateTimeNode::class,'datetime'],
        'float'=>[FloatNode::class,'float'],
        'integer'=>[IntegerNode::class,'integer'],
        'string'=>[StringNode::class,'string'],
        'time'=>[TimeNode::class,'time'],
    ]);