<?php
/**
 * @file QueryNodeTest.php
 * tests: /src/Query/QueryParser/QueryNode.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Query\QueryParser\Nodes\OrderNode;
use Sunhill\Query\Exceptions\InvalidOrderException;
use Sunhill\Query\QueryParser\Nodes\QueryNode;
use Sunhill\Query\Exceptions\InvalidStatementException;

uses(SunhillTestCase::class);

test('offset works for 0', function()
{
    $test = new QueryNode();
    $test->offset(0);
    $test->validate();
    expect($test->offset())->toBe(0);
});

test('offset works for positive numbers', function()
{
    $test = new QueryNode();
    $test->offset(10);
    $test->validate();
    expect($test->offset())->toBe(10);
});

test('offset fails for negative numbers', function()
{
    $test = new QueryNode();
    $test->offset(-10);
    $test->validate();
})->throws(InvalidStatementException::class);