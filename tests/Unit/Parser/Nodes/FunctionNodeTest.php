<?php

/**
 * @file FunctionNodeTest.php
 * tests: /src/Parser/Nodes/FunctionNode.php
 * free of dependent units: yes
 */

use Sunhill\Parser\Exceptions\AnalyzerException;
use Sunhill\Parser\LanguageDescriptor\FunctionDescriptor;
use Sunhill\Parser\Nodes\ArrayNode;
use Sunhill\Parser\Nodes\FunctionNode;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

test('getDatatype() of function with a type set', function () {
    $descriptor = \Mockery::mock(FunctionDescriptor::class);
    $descriptor->shouldReceive('getReturnType')->andReturn('integer');
    $test = new FunctionNode('test');
    $test->setFunctionDescriptor($descriptor);
    expect($test->getDatatype())->toBe('integer');
});

test('getDatatype() of function without a type set', function () {
    $test = new FunctionNode('test');
    expect($test->getDatatype())->toBe(null);
});

test('toString()', function ($modifier, $expect) {
    expect($modifier()->toString())->toBe($expect);
})->with(
    [
        'FunctionNode without arguments' => [function () {
            return new FunctionNode('test');
        }, 'test()'],
        'FunctionNode with one argument' => [function () {
            $argument = \Mockery::mock(Node::class);
            $argument->shouldReceive('toString')->once()->andReturn('10');
            $test = new FunctionNode('test');
            $test->arguments($argument);

            return $test;
        }, 'test(10)'],
        'FunctionNode with two arguments' => [function () {
            $argument1 = \Mockery::mock(Node::class);
            $argument1->shouldReceive('toString')->andReturn('10');
            $argument2 = \Mockery::mock(Node::class);
            $argument2->shouldReceive('toString')->andReturn('"abc"');
            $arguments = \Mockery::mock(ArrayNode::class);
            $arguments->shouldReceive('elementCount')->andReturn(2);
            $arguments->shouldReceive('getElement')->andReturn($argument1, $argument2);
            $test = new FunctionNode('testfunc');
            $test->arguments($arguments);

            return $test;
        }, 'testfunc(10,"abc")'],
    ]);

test('validate()', function ($modifier, $expect) {
    $result = true;
    try {
        $modifier()->validate();
    } catch (AnalyzerException $e) {
        $result = false;
    }
    expect($result)->toBe($expect);
})->with(
    [
        'Unknown Function' => [function () {
            return new FunctionNode('test');
        }, false],
        'Function without arguments passes' => [function () {
            $descriptor = \Mockery::mock(FunctionDescriptor::class);
            $descriptor->shouldReceive('reset');
            $descriptor->shouldReceive('pop')->andReturn(null);
            $funct = new FunctionNode('test');
            $funct->setFunctionDescriptor($descriptor);

            return $funct;
        }, true],
        'Function with one argument passes' => [function () {
            $descriptor = \Mockery::mock(FunctionDescriptor::class);
            $descriptor->shouldReceive('reset');
            $descriptor->shouldReceive('pop')->andReturn('!integer', null);

            $argument = \Mockery::mock(Node::class);
            $argument->shouldReceive('getDatatype')->once()->andReturn('integer');
            $test = new FunctionNode('test');
            $test->arguments($argument);
            $test->setFunctionDescriptor($descriptor);

            return $test;
        }, true],
        'Function with one argument fails due type mismatch' => [function () {
            $descriptor = \Mockery::mock(FunctionDescriptor::class);
            $descriptor->shouldReceive('reset');
            $descriptor->shouldReceive('pop')->andReturn('!string', null);

            $argument = \Mockery::mock(Node::class);
            $argument->shouldReceive('getDatatype')->once()->andReturn('integer');
            $test = new FunctionNode('test');
            $test->arguments($argument);
            $test->setFunctionDescriptor($descriptor);

            return $test;
        }, false],
        'Function with one argument fails due missing argument' => [function () {
            $descriptor = \Mockery::mock(FunctionDescriptor::class);
            $descriptor->shouldReceive('reset');
            $descriptor->shouldReceive('pop')->andReturn('!integer', null);
            $funct = new FunctionNode('test');
            $funct->setFunctionDescriptor($descriptor);

            return $funct;
        }, false],
        'Function with one mandatory and one optional argument passes (both given)' => [function () {
            $descriptor = \Mockery::mock(FunctionDescriptor::class);
            $descriptor->shouldReceive('reset');
            $descriptor->shouldReceive('pop')->andReturn('!integer', '?string');
            $argument1 = \Mockery::mock(Node::class);
            $argument1->shouldReceive('getDatatype')->andReturn('integer');
            $argument2 = \Mockery::mock(Node::class);
            $argument2->shouldReceive('getDatatype')->andReturn('string');
            $arguments = \Mockery::mock(ArrayNode::class);
            $arguments->shouldReceive('elementCount')->andReturn(2);
            $arguments->shouldReceive('getElement')->andReturn($argument1, $argument2);
            $test = new FunctionNode('testfunc');
            $test->arguments($arguments);
            $test->setFunctionDescriptor($descriptor);

            return $test;
        }, true],
        'Function with one mandatory and one optional argument passes (optional omitted)' => [function () {
            $descriptor = \Mockery::mock(FunctionDescriptor::class);
            $descriptor->shouldReceive('reset');
            $descriptor->shouldReceive('pop')->andReturn('!integer', '?string');
            $argument1 = \Mockery::mock(Node::class);
            $argument1->shouldReceive('getDatatype')->andReturn('integer');
            $test = new FunctionNode('testfunc');
            $test->arguments($argument1);
            $test->setFunctionDescriptor($descriptor);

            return $test;
        }, true],
        'Function with two mandatory fails (one omitted)' => [function () {
            $descriptor = \Mockery::mock(FunctionDescriptor::class);
            $descriptor->shouldReceive('reset');
            $descriptor->shouldReceive('pop')->andReturn('!integer', '!string');
            $argument1 = \Mockery::mock(Node::class);
            $argument1->shouldReceive('getDatatype')->andReturn('integer');
            $test = new FunctionNode('testfunc');
            $test->arguments($argument1);
            $test->setFunctionDescriptor($descriptor);

            return $test;
        }, false],

    ]);

test('getArgumentCount() for FunctionNode', function ($modifier, $expect) {
    expect($modifier()->getArgumentCount())->toBe($expect);
})->with([
    'function with no arguments' => [function () {
        return new FunctionNode('testfunct');
    }, 0],
    'function with one argument' => [function () {

        $result = new FunctionNode('testfunct');
        $result->arguments(\Mockery::mock(Node::class));

        return $result;
    }, 1],
    'function with array node as arguments' => [function () {
        $arguments = \Mockery::mock(ArrayNode::class);
        $arguments->shouldReceive('elementCount')->andReturn(2);

        $test = new FunctionNode('testfunc');
        $test->arguments($arguments);

        return $test;
    }, 2],
]);

test('name() for function', function () {
    $test = new FunctionNode('testfunc');
    expect($test->name())->toBe('testfunc');
});

test('FunctionNode with single node', function () {
    $node = \Mockery::mock(Node::class);
    $test = new FunctionNode('testfunc');
    $test->arguments($node);
    expect($test->getArgument(0))->toBe($node);
});
