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
use Sunhill\Parser\Nodes\FunctionNode;
use Sunhill\Parser\LanguageDescriptor\LanguageDescriptor;
use Sunhill\Parser\LanguageDescriptor\FunctionDescriptor;
use Sunhill\Query\Exceptions\InvalidStatementException;
use Sunhill\Query\Exceptions\InsufficentQueryException;

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

test('analyze fails when no LanguageDescriptor is set', function()
{
    $record = \Mockery::mock(RecordProperty::class);
    $test = new QueryAnalyzer($record);
    $node = \Mockery::mock(FunctionNode::class);
    $node->shouldReceive('getName')->once()->andReturn('test');
    
    $test->analyze($node);
})->throws(InsufficentQueryException::class);

test('analyze known function', function()
{
    $profile = \Mockery::mock(FunctionDescriptor::class);
    $node = \Mockery::mock(FunctionNode::class);
    $node->shouldReceive('getName')->once()->andReturn('test');
    $node->shouldReceive('getArgumentCount')->once()->andReturn(0);
    $node->shouldReceive('getDatatype')->once()->andReturn('integer');
    $node->shouldReceive('setFunctionDescriptor')->with($profile)->once();
    $node->shouldReceive('validate')->once();
    $language = \Mockery::mock(LanguageDescriptor::class);
    $language->shouldReceive('getFunctionProfile')->once()->with('test')->andReturn($profile);
    $record = \Mockery::mock(RecordProperty::class);
    
    $test = new QueryAnalyzer($record);
    $test->setLanguageDescriptor($language);
    $test->analyze($node);
});

test('analyze unknown function', function()
{
    $node = \Mockery::mock(FunctionNode::class);
    $node->shouldReceive('getName')->once()->andReturn('test');
    $language = \Mockery::mock(LanguageDescriptor::class);
    $language->shouldReceive('getFunctionProfile')->once()->with('test')->andReturn(null);
    $record = \Mockery::mock(RecordProperty::class);
    
    $test = new QueryAnalyzer($record);
    $test->setLanguageDescriptor($language);
    $test->analyze($node);    
})->throws(InvalidStatementException::class);