<?php

/**
 * @file FunctionNodeTest.php
 * tests: /src/Parser/Nodes/FunctionNode.php
 * free of dependent units: yes
 */

use Sunhill\Parser\Exceptions\TypesMismatchException;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Parser\Nodes\UnaryNode;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

test('UnaryNode getType()', function () {
    $node = \Mockery::mock(Node::class);
    $test = new UnaryNode('+');
    $test->child($node);

    expect($test->getType())->toBe('+');
});

test('UnaryNode child()', function () {
    $node = \Mockery::mock(Node::class);
    $test = new UnaryNode('+');
    $test->child($node);

    expect($test->child())->toBe($node);
});

test('UnaryNode getDatatype()', function () {
    $node = \Mockery::mock(Node::class);
    $node->shouldReceive('getDatatype')->once()->andReturn('integer');
    $test = new UnaryNode('+');
    $test->child($node);
    $test->addAllowedType('integer', 'float');
    $test->addAllowedType('date', 'integer');

    expect($test->getDatatype())->toBe('float');
});

test('validate() pass', function () {
    $node = \Mockery::mock(Node::class);
    $node->shouldReceive('validate');
    $node->shouldReceive('getDatatype')->once()->andReturn('integer');
    $test = new UnaryNode('+');
    $test->child($node);
    $test->addAllowedType('integer', 'float');
    $test->addAllowedType('date', 'integer');
    $test->validate();
});

test('validate() fail', function () {
    $node = \Mockery::mock(Node::class);
    $node->shouldReceive('validate');
    $node->shouldReceive('getDatatype')->once()->andReturn('string');
    $test = new UnaryNode('+');
    $test->child($node);
    $test->addAllowedType('integer', 'float');
    $test->addAllowedType('date', 'integer');
    $test->validate();
})->throws(TypesMismatchException::class);
