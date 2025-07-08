<?php
/**
 * @file QueryNodeTest.php
 * tests: /src/Query/QueryParser/QueryNode.php
 * free of dependent units: no (QueryNode->fields() creates an ArrayNode()
 */

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Query\QueryParser\Nodes\QueryNode;
use Sunhill\Query\Exceptions\InvalidStatementException;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Parser\Nodes\ArrayNode;
use Sunhill\Parser\Nodes\Node;

uses(SunhillTestCase::class);

test('validating an all empty query works', function()
{
    $test = new QueryNode();
    $test->validate();
    expect(true)->toBe(true); // To avoid a warning
});

test('fields validate works one field', function()
{
    $field = \Mockery::mock(IdentifierNode::class);
    $field->shouldReceive('validate')->once();
    $test = new QueryNode();
    $test->fields($field);
    $test->validate();    
    expect($test->fields())->toBe($field);
});

test('fields validate works two fields', function()
{
    $field1 = \Mockery::mock(IdentifierNode::class);
    $field1->shouldReceive('validate')->once();
    $field2 = \Mockery::mock(IdentifierNode::class);
    $field2->shouldReceive('validate')->once();
    $test = new QueryNode();
    $test->fields($field1);
    $test->fields($field2);
    $test->validate();
    expect(is_a($test->fields(),ArrayNode::class))->toBe(true);
});

test('group validate works one field', function()
{
    $field = \Mockery::mock(IdentifierNode::class);
    $field->shouldReceive('validate')->once();
    $test = new QueryNode();
    $test->group($field);
    $test->validate();
    expect($test->group())->toBe($field);
});

test('group validate works two group', function()
{
    $field1 = \Mockery::mock(IdentifierNode::class);
    $field1->shouldReceive('validate')->once();
    $field2 = \Mockery::mock(IdentifierNode::class);
    $field2->shouldReceive('validate')->once();
    $test = new QueryNode();
    $test->group($field1);
    $test->group($field2);
    $test->validate();
    expect(is_a($test->group(),ArrayNode::class))->toBe(true);
});

test('order validate works one field', function()
{
    $field = \Mockery::mock(IdentifierNode::class);
    $field->shouldReceive('validate')->once();
    $test = new QueryNode();
    $test->order($field);
    $test->validate();
    expect($test->order())->toBe($field);
});

test('order validate works two fields', function()
{
    $field1 = \Mockery::mock(IdentifierNode::class);
    $field1->shouldReceive('validate')->once();
    $field2 = \Mockery::mock(IdentifierNode::class);
    $field2->shouldReceive('validate')->once();
    $test = new QueryNode();
    $test->order($field1);
    $test->order($field2);
    $test->validate();
    expect(is_a($test->order(),ArrayNode::class))->toBe(true);
});

test('offset works with empty field', function()
{
    $test = new QueryNode();
    expect($test->offset())->toBe(null);
});

test('offset validate works for 0', function()
{
    $test = new QueryNode();
    $test->offset(0);
    $test->validate();
    expect($test->offset())->toBe(0);
});

test('offset validate works for positive numbers', function()
{
    $test = new QueryNode();
    $test->offset(10);
    $test->validate();
    expect($test->offset())->toBe(10);
});

test('offset validate fails for negative numbers', function()
{
    $test = new QueryNode();
    $test->offset(-10);
    $test->validate();
})->throws(InvalidStatementException::class);

test('limit validate fails for 0', function()
{
    $test = new QueryNode();
    $test->limit(0);
    $test->validate();
})->throws(InvalidStatementException::class);

test('limit validate works for positive numbers', function()
{
    $test = new QueryNode();
    $test->limit(10);
    $test->validate();
    expect($test->limit())->toBe(10);
});

test('limit validate fails for negative numbers', function()
{
    $test = new QueryNode();
    $test->limit(-10);
    $test->validate();
})->throws(InvalidStatementException::class);

test('where validate works', function()
{
    $node = \Mockery::mock(Node::class);
    $node->shouldReceive('validate')->once();
    $test = new QueryNode();
    $test->where($node);
    
    $test->validate();
    expect($test->where())->toBe($node);
});

test('having validate works', function()
{
    $node = \Mockery::mock(Node::class);
    $node->shouldReceive('validate')->once();
    $test = new QueryNode();
    $test->having($node);
    
    $test->validate();
    expect($test->having())->toBe($node);
});

test('verb validate passes', function()
{
    $test = new QueryNode();
    $test->verb('delete');
    $test->validate();
    expect($test->verb())->toBe('delete');
});

test('verb validate fails', function()
{
    $test = new QueryNode();
    $test->verb('nonexisting');
    $test->validate();
})->throws(InvalidStatementException::class);
