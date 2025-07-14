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