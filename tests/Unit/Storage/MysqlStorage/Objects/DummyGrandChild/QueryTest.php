<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\DummyGrandChild;
use Sunhill\Query\QueryParser\QueryNode;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Query', function($manipulator, $expectation)
{
    $test = prepareStorage(DummyGrandChild::class, $this);
    $query_node = new QueryNode();
    $query_node->addStorage('dummygrandchildren');
    $manipulator($query_node);
    $result = $test->executeQuery($query_node);
    if (is_callable($expectation)) {
        $expectation($result);
    } else {
        expect($result)->toBe($expectation);
    }
})->group('query')->with(
    [
        'simple count'=>[
            function($node)
            {
                $node->verb('count');
            },
            1
        ],
        'get all with nothing more'=>[
            function($node)
            {
                $node->verb('get');
            },
            function ($result)
            {
                expect($result[0]->id)->toBe(15);
            }
        ],
        'first with nothing more'=>[
            function($node)
            {
                $node->verb('first');
            },
            function ($result)
            {
                expect($result->id)->toBe(15);
            }],
        ]);