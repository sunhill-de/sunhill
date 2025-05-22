<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\ParentReference;
use Sunhill\Query\QueryParser\QueryNode;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Query', function($manipulator, $expectation)
{
    $test = prepareStorage(ParentReference::class, $this);
    $query_node = new QueryNode();
    $query_node->addStorage('parentreferences');
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
        3
        ],
        'get all with nothing more'=>[
            function($node)
            {
                $node->verb('get');
        },
        function ($result)
        {
            checkResultArray($result, [17,18,19]);
        }
        ],
        'first with nothing more'=>[
            function($node)
            {
                $node->verb('first');
        },
        function ($result)
        {
            expect($result->id)->toBeIn([17,18,19]);
        }],
        ]);