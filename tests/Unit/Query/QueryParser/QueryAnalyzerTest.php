<?php
/**
 * @file QueryAnalyzerTest.php
 * tests: /src/Query/QueryParser/QueryAnalyzer.php
 * free of dependent units: 
 */

use Sunhill\Tests\SunhillSimpleTestCase;
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
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Parser\LanguageDescriptor\OperatorDescriptor;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Parser\Nodes\UnaryNode;

uses(SunhillSimpleTestCase::class);

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

test('analyze known binary operator', function()
{
    $left = \Mockery::mock(Node::class);
    $right = \Mockery::mock(Node::class);
    
    $node = \Mockery::mock(BinaryNode::class);
    $node->shouldReceive('validate')->once();
    $node->shouldReceive('getType')->andReturn('+');
    $node->shouldReceive('addAllowedType')->with('integer','integer','integer');
    $node->shouldReceive('left')->andReturn($left);
    $node->shouldReceive('right')->andReturn($right);
    $node->shouldReceive('getDatatype')->andReturn('integer');
    $descriptor = \Mockery::mock(OperatorDescriptor::class);
    $descriptor->shouldReceive('getAcceptedTypes')->andReturn([['left'=>'integer','right'=>'integer','resulting'=>'integer']]);
    $language = \Mockery::mock(LanguageDescriptor::class);
    $language->shouldReceive('getBinaryOperator')->with('+')->once()->andReturn($descriptor);
    $record = \Mockery::mock(RecordProperty::class);
    
    $test = new QueryAnalyzer($record);
    $test->setLanguageDescriptor($language);
    $test->analyze($node);
});

test('analyze unknown binary operator', function()
{
    $node = \Mockery::mock(BinaryNode::class);
    $node->shouldReceive('getType')->andReturn('%');
    $node->shouldReceive('validate')->once();
    $node->shouldReceive('getDatatype')->once()->andReturn('unknown');
    $language = \Mockery::mock(LanguageDescriptor::class);
    $language->shouldReceive('getBinaryOperator')->with('%')->once()->andReturn(null);
    $record = \Mockery::mock(RecordProperty::class);

    $test = new QueryAnalyzer($record);
    $test->setLanguageDescriptor($language);
    $test->analyze($node);    
});

test('analyze known unary operator', function()
{
    $child = \Mockery::mock(Node::class);
    
    $node = \Mockery::mock(UnaryNode::class);
    $node->shouldReceive('validate')->once();
    $node->shouldReceive('getType')->andReturn('+');
    $node->shouldReceive('addAllowedType')->with('integer','integer');
    $node->shouldReceive('child')->andReturn($child);
    $node->shouldReceive('getDatatype')->andReturn('integer');
    $descriptor = \Mockery::mock(OperatorDescriptor::class);
    $descriptor->shouldReceive('getAcceptedTypes')->andReturn([['child'=>'integer','resulting'=>'integer']]);
    $language = \Mockery::mock(LanguageDescriptor::class);
    $language->shouldReceive('getUnaryOperator')->with('+')->once()->andReturn($descriptor);
    $record = \Mockery::mock(RecordProperty::class);
    
    $test = new QueryAnalyzer($record);
    $test->setLanguageDescriptor($language);
    $test->analyze($node);
});

test('analyze unknown unary operator', function()
{
    $node = \Mockery::mock(UnaryNode::class);
    $node->shouldReceive('getType')->andReturn('%');
    $node->shouldReceive('validate')->once();
    $node->shouldReceive('getDatatype')->once()->andReturn('unknown');
    $language = \Mockery::mock(LanguageDescriptor::class);
    $language->shouldReceive('getUnaryOperator')->with('%')->once()->andReturn(null);
    $record = \Mockery::mock(RecordProperty::class);
    
    $test = new QueryAnalyzer($record);
    $test->setLanguageDescriptor($language);
    $test->analyze($node);
});
