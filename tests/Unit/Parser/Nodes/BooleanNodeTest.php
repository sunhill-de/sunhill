<?php
/**
 * @file BooleanNodeTest.php
 * tests: /src/Parser/Nodes/BooleanNode.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Parser\Nodes\ArrayNode;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Parser\Nodes\BooleanNode;

uses(SunhillTestCase::class);

test('getDatatype()', function()
{
    $test = new BooleanNode(true);
    expect($test->getDatatype())->toBe('boolean');
});

test('getValue()', function()
{
    $test = new BooleanNode(true);
    expect($test->getValue())->toBe(true);
});

test('toString() with true', function()
{
    $test = new BooleanNode(true);
    expect($test->toString())->toBe("true");
});

test('toString() with false', function()
{
    $test = new BooleanNode(false);
    expect($test->toString())->toBe("false");
});

test('validate()', function()
{
    $test = new BooleanNode(false);
    $test->validate();
    expect(true)->toBe(true);
});
