<?php

/**
 * @file FunctionNodeTest.php
 * tests: /src/Parser/Nodes/FunctionNode.php
 * free of dependent units: yes
 */

use Sunhill\Parser\Exceptions\AnalyzerException;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Tests\SunhillSimpleTestCase;

uses(SunhillSimpleTestCase::class);

test('getDatatype()', function ($modifier, $expect) {
    expect($modifier()->getDatatype())->toBe($expect);
})->with(
    [
        'IdentifierNode (type set)' => [function () {
            $return = new IdentifierNode('test');
            $return->setDatatype('integer');

            return $return;
        }, 'integer'],
        'IdentifierNode (type not set)' => [function () {
            return new IdentifierNode('test');
        }, null],
    ]);

test('toString()', function ($modifier, $expect) {
    expect($modifier()->toString())->toBe($expect);
})->with(
    [
        'IdentifierNode (type set)' => [function () {
            $return = new IdentifierNode('test');
            $return->setDatatype('integer');

            return $return;
        }, 'test'],
        'IdentifierNode (type not set)' => [function () {
            return new IdentifierNode('test');
        }, 'test'],
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
        'IdentifierNode (type set)' => [function () {
            $return = new IdentifierNode('test');
            $return->setDatatype('integer');

            return $return;
        }, true],
        'IdentifierNode (type not set)' => [function () {
            return new IdentifierNode('test');
        }, false],
    ]);
