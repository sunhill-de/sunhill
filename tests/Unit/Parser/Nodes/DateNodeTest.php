<?php
/**
 * @file DateNodeTest.php
 * tests: /src/Parser/Nodes/DateNode.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillSimpleTestCase;
use Sunhill\Parser\Nodes\DateNode;

uses(SunhillSimpleTestCase::class);

test('getDatatype()', function()
{
    $test = new DateNode('2025-07-08');
    expect($test->getDatatype())->toBe('date');
});

test('getValue()', function()
{
    $test = new DateNode('2025-07-08');
    expect($test->getValue())->toBe('2025-07-08');
});

test('toString()', function()
{
    $test = new DateNode('2025-07-08');
    expect($test->toString())->toBe('"2025-07-08"');
});

test('validate()', function()
{
    $test = new DateNode(false);
    $test->validate();
    expect('2025-07-08')->toBe('2025-07-08');
});
