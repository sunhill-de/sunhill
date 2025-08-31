<?php
/**
 * @file ReferenceNodeTest.php
 * tests: /src/Parser/Nodes/ReferenceNode.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Parser\Nodes\ReferenceNode;
use Sunhill\Query\Exceptions\InvalidStatementException;

uses(SunhillSimpleTestCase::class);

test('getDatatype() of reference with a type set', function()
{
    $reference = \Mockery::mock(Node::class);
    $reference->shouldReceive('getDatatype')->once()->andReturn('integer');
    $test = new ReferenceNode('test', $reference);
    expect($test->getDatatype())->toBe('integer');
});

test('getDatatype() of function without a type set', function()
{
    $reference = \Mockery::mock(Node::class);
    $reference->shouldReceive('getDatatype')->once()->andReturn(null);
    $test = new ReferenceNode('test', $reference);
    expect($test->getDatatype())->toBe(null);
});

test('toString() works', function()
{
    $reference = \Mockery::mock(Node::class);
    $reference->shouldReceive('toString')->once()->andReturn('sub');
    $test = new ReferenceNode('main', $reference);
    expect($test->toString())->toBe('main->sub');
    
});

test('getName() works', function()
{
    $reference = \Mockery::mock(Node::class);
    $test = new ReferenceNode('main', $reference);
    expect($test->getName())->toBe('main');    
});

test('validate() works', function()
{
    $reference = \Mockery::mock(Node::class);
    $reference->shouldReceive('validate')->once();
    $test = new ReferenceNode('main', $reference);
    $test->validate();
});

test('validate() fails when no reference is set', function()
{
    $test = new ReferenceNode('main');
    $test->validate();
})->throws(InvalidStatementException::class);


