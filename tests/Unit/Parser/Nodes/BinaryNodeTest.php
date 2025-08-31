<?php

/**
 * @file BinaryNodeTest.php
 * tests: /src/Parser/Nodes/BinaryNode.php
 * free of dependent units: yes
 */

use Sunhill\Parser\Exceptions\TypesMismatchException;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

test('getType()', function () {
    $left = \Mockery::mock(Node::class);
    $right = \Mockery::mock(Node::class);
    $test = new BinaryNode('+');
    $test->left($left);
    $test->right($right);

    expect($test->getType())->toBe('+');
});

test('left() and right()', function () {
    $left = \Mockery::mock(Node::class);
    $right = \Mockery::mock(Node::class);
    $test = new BinaryNode('+');
    $test->left($left);
    $test->right($right);

    expect($test->left())->toBe($left);
    expect($test->right())->toBe($right);
});

test('getDatatype()', function () {
    $left = \Mockery::mock(Node::class);
    $left->shouldReceive('getDatatype')->andReturn('integer');
    $right = \Mockery::mock(Node::class);
    $right->shouldReceive('getDatatype')->andReturn('float');
    $test = new BinaryNode('+');
    $test->left($left);
    $test->right($right);
    $test->addAllowedType('integer', 'integer', 'integer');
    $test->addAllowedType('float', 'float', 'float');
    $test->addAllowedType('float', 'integer', 'float');
    $test->addAllowedType('integer', 'float', 'float');
    expect($test->getDatatype())->toBe('float');
});

test('validate() passes', function () {
    $left = \Mockery::mock(Node::class);
    $left->shouldReceive('getDatatype')->once()->andReturn('integer');
    $left->shouldReceive('validate')->once();
    $right = \Mockery::mock(Node::class);
    $right->shouldReceive('getDatatype')->once()->andReturn('float');
    $right->shouldReceive('validate')->once();
    $test = new BinaryNode('+');
    $test->left($left);
    $test->right($right);
    $test->addAllowedType('integer', 'integer', 'integer');
    $test->addAllowedType('float', 'float', 'float');
    $test->addAllowedType('float', 'integer', 'float');
    $test->addAllowedType('integer', 'float', 'float');
    $test->validate();
});

test('validate() fails', function () {
    $left = \Mockery::mock(Node::class);
    $left->shouldReceive('getDatatype')->once()->andReturn('integer');
    $left->shouldReceive('validate')->once();
    $right = \Mockery::mock(Node::class);
    $right->shouldReceive('getDatatype')->once()->andReturn('string');
    $right->shouldReceive('validate')->once();
    $test = new BinaryNode('+');
    $test->left($left);
    $test->right($right);
    $test->addAllowedType('integer', 'integer', 'integer');
    $test->addAllowedType('float', 'float', 'float');
    $test->addAllowedType('float', 'integer', 'float');
    $test->addAllowedType('integer', 'float', 'float');
    $test->validate();
})->throws(TypesMismatchException::class);
