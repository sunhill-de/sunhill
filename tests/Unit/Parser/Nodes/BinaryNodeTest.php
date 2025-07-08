<?php
/**
 * @file BinaryNodeTest.php
 * tests: /src/Parser/Nodes/BinaryNode.php
 * free of dependent units: yes
 */

use Sunhill\Tests\SunhillTestCase;
use Sunhill\Parser\Nodes\ArrayNode;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Parser\Nodes\BinaryNode;

uses(SunhillTestCase::class);

test('BinaryNode', function()
{
    $left = \Mockery::mock(Node::class);
    $right = \Mockery::mock(Node::class);
    $test = new BinaryNode('+');
    $test->left($left);
    $test->right($right);
    
    expect($test->getType())->toBe('+');
    expect($test->left())->toBe($left);
    expect($test->right())->toBe($right);
});

