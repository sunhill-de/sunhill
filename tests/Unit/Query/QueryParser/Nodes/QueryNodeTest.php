<?php
/**
 * @file QueryNodeTest.php
 * tests: /src/Query/QueryParser/QueryNode.php
 * free of dependent units: no (QueryNode->fields() creates an ArrayNode()
 */

use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Query\QueryParser\Nodes\QueryNode;
use Sunhill\Query\Exceptions\InvalidStatementException;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Parser\Nodes\ArrayNode;
use Sunhill\Parser\Nodes\Node;

uses(SunhillSimpleTestCase::class);

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

test('addStorage() with new storage and default values', function()
{
    $test = new QueryNode();
    expect($test->addStorage('teststorage'))->toBe('a');
    $storages = $test->getStorages();
    expect(count($storages))->toBe(1);
    expect($storages['a']->storage)->toBe('teststorage');
    expect($storages['a']->join)->toBe('first');
    expect($storages['a']->target)->toBe(null);
    expect($storages['a']->field)->toBe(null);
    expect($storages['a']->target_field)->toBe(null);
});

test('addStorage() with known storage and default values', function()
{
    $test = new QueryNode();
    $test->addStorage('teststorage');
    expect($test->addStorage('teststorage'))->toBe('a');
    $storages = $test->getStorages();
    expect(count($storages))->toBe(1);
});

test('addStorage() with old and new storage and default values', function()
{
    $test = new QueryNode();
    $test->addStorage('teststorage');
    expect($test->addStorage('anotherone'))->toBe('b');
    $storages = $test->getStorages();
    expect(count($storages))->toBe(2);
    expect($storages['b']->storage)->toBe('anotherone');
    expect($storages['b']->join)->toBe('inner');
    expect($storages['b']->target)->toBe('teststorage');
    expect($storages['b']->field)->toBe('id');
    expect($storages['b']->target_field)->toBe('id');
});

test('addStorage() with old and new storage with non-default target', function()
{
    $test = new QueryNode();
    $test->addStorage('teststorage');
    $test->addStorage('anotherone');
    expect($test->addStorage('anotherone','left','anotherone'))->toBe('c');
    $storages = $test->getStorages();
    expect(count($storages))->toBe(3);
    expect($storages['c']->storage)->toBe('anotherone');
    expect($storages['c']->join)->toBe('left');
    expect($storages['c']->target)->toBe('anotherone');
    expect($storages['c']->field)->toBe('id');
    expect($storages['c']->target_field)->toBe('id');
});

test('addStorage() with old and new storage with non-default target fields', function()
{
    $test = new QueryNode();
    $test->addStorage('teststorage');
    $test->addStorage('anotherone');
    expect($test->addStorage('anotherone','left','anotherone', 'other', 'allother'))->toBe('c');
    $storages = $test->getStorages();
    expect(count($storages))->toBe(3);
    expect($storages['c']->storage)->toBe('anotherone');
    expect($storages['c']->join)->toBe('left');
    expect($storages['c']->target)->toBe('anotherone');
    expect($storages['c']->field)->toBe('other');
    expect($storages['c']->target_field)->toBe('allother');
});

test('addStorage() with a different join to the main table', function()
{
    $test = new QueryNode();
    $test->addStorage('teststorage');
    expect($test->addStorage('teststorage','left','teststorage', 'other'))->toBe('b');
    $storages = $test->getStorages();
    expect(count($storages))->toBe(2);
    expect($storages['b']->storage)->toBe('teststorage');
    expect($storages['b']->join)->toBe('left');
    expect($storages['b']->target)->toBe('teststorage');
    expect($storages['b']->field)->toBe('other');
    expect($storages['b']->target_field)->toBe('id');
});

test('toString()', function($manipulator, $expect)
{
    $test = $manipulator();
    expect($test->toString())->toBe($expect);
})->with(
    [
        'empty query without storageids'=>[function() 
        {
            return new QueryNode();
        },'SELECT * FROM '],
        'empty query with one storageid'=>[function() 
        {
            $return = new QueryNode();
            $return->addStorage('somestorage');
            return $return;
        },'SELECT * FROM somestorage AS a'],
        'empty query with two storageid'=>[function() 
        {
            $return = new QueryNode();
            $return->addStorage('somestorage');
            $return->addStorage('anotherstorage');
            return $return;
        },'SELECT * FROM somestorage AS a INNER JOIN anotherstorage AS b ON b.id = a.id'],
        'empty query with two storageid'=>[function() {
            $return = new QueryNode();
            $return->addStorage('somestorage');
            $return->addStorage('anotherstorage');
            $return->addStorage('thirdstorage');
            return $return;
        },'SELECT * FROM somestorage AS a INNER JOIN anotherstorage AS b ON b.id = a.id INNER JOIN thirdstorage AS c ON c.id = a.id'],
        'empty query with one field'=>[function()
        {
            $field = \Mockery::mock(Node::class);
            $field->shouldReceive('toString')->andReturn('[single field]');
            $return = new QueryNode();
            $return->addStorage('somestorage');
            $return->fields($field);
            return $return;
        },'SELECT [single field] FROM somestorage AS a'],
        'empty query with more field'=>[function()
        {
            $field1 = \Mockery::mock(Node::class);
            $field1->shouldReceive('toString')->andReturn('[field1]');
            $field2 = \Mockery::mock(Node::class);
            $field2->shouldReceive('toString')->andReturn('[field2]');
            $field3 = \Mockery::mock(Node::class);
            $field3->shouldReceive('toString')->andReturn('[field3]');
            $fields = \Mockery::mock(ArrayNode::class);
            $fields->shouldReceive('elementCount')->andReturn(3);
            $fields->shouldReceive('getElement')->andReturn($field1,$field2,$field3);
            $return = new QueryNode();
            $return->addStorage('somestorage');
            $return->fields($fields);
            return $return;
        },'SELECT [field1], [field2], [field3] FROM somestorage AS a'],
        'query with offset'=>[function()
        {
            $return = new QueryNode();
            $return->addStorage('somestorage');
            $return->offset(10);
            return $return;
        },'SELECT * FROM somestorage AS a OFFSET 10'],
        'query with limit'=>[function()
        {
            $return = new QueryNode();
            $return->addStorage('somestorage');
            $return->limit(10);
            return $return;
        },'SELECT * FROM somestorage AS a LIMIT 10'],
        'query with where'=>[function()
        {
            $node = \Mockery::mock(Node::class);
            $node->shouldReceive('toString')->andReturn('[where statement]');
            $return = new QueryNode();
            $return->addStorage('somestorage');
            $return->where($node);
            return $return;
        },'SELECT * FROM somestorage AS a WHERE [where statement]'],
        'query with order'=>[function()
        {
            $node = \Mockery::mock(Node::class);
            $node->shouldReceive('toString')->andReturn('[order statement]');
            $return = new QueryNode();
            $return->addStorage('somestorage');
            $return->order($node);
            return $return;
        },'SELECT * FROM somestorage AS a ORDER BY [order statement]'],
        'query with having'=>[function()
        {
            $node = \Mockery::mock(Node::class);
            $node->shouldReceive('toString')->andReturn('[having statement]');
            $return = new QueryNode();
            $return->addStorage('somestorage');
            $return->having($node);
            return $return;
        },'SELECT * FROM somestorage AS a HAVING [having statement]'],
        'query with everything'=>[function()
        {
            $field1 = \Mockery::mock(Node::class);
            $field1->shouldReceive('toString')->andReturn('[field1]');
            $field2 = \Mockery::mock(Node::class);
            $field2->shouldReceive('toString')->andReturn('[field2]');
            $field3 = \Mockery::mock(Node::class);
            $field3->shouldReceive('toString')->andReturn('[field3]');
            $fields = \Mockery::mock(ArrayNode::class);
            $fields->shouldReceive('elementCount')->andReturn(3);
            $fields->shouldReceive('getElement')->andReturn($field1,$field2,$field3);
            $where = \Mockery::mock(Node::class);
            $where->shouldReceive('toString')->andReturn('[where statement]');
            $group = \Mockery::mock(Node::class);
            $group->shouldReceive('toString')->andReturn('[group statement]');
            $having = \Mockery::mock(Node::class);
            $having->shouldReceive('toString')->andReturn('[having statement]');
            $order = \Mockery::mock(Node::class);
            $order->shouldReceive('toString')->andReturn('[order statement]');
            $return = new QueryNode();
            $return->addStorage('somestorage');
            $return->addStorage('anotherstorage');
            $return->fields($fields);
            $return->where($where);
            $return->group($group);
            $return->having($having);
            $return->order($order);
            $return->offset(10);
            $return->limit(20);
            
            return $return;
        },'SELECT [field1], [field2], [field3] FROM somestorage AS a INNER JOIN anotherstorage AS b ON b.id = a.id '.
           'WHERE [where statement] GROUP BY [group statement] HAVING [having statement] ORDER BY [order statement] OFFSET 10 LIMIT 20'],
        ]);