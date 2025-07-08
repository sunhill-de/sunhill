<?php
/**
 * @file DateTimeNodeTest.php
 * tests: /src/Parser/Nodes/DateTimeNode.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Parser\Nodes\DateTimeNode;

uses(SunhillTestCase::class);

test('getDatatype()', function()
{
    $test = new DateTimeNode('2025-07-08 11:12:13');
    expect($test->getDatatype())->toBe('datetime');
});

test('getValue()', function()
{
    $test = new DateTimeNode('2025-07-08 11:12:13');
    expect($test->getValue())->toBe('2025-07-08 11:12:13');
});

test('toString()', function()
{
    $test = new DateTimeNode('2025-07-08 11:12:13');
    expect($test->toString())->toBe('"2025-07-08 11:12:13"');
});

test('validate()', function()
{
    $test = new DateTimeNode(false);
    $test->validate();
    expect('2025-07-08 11:12:13')->toBe('2025-07-08 11:12:13');
});
