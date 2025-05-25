<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Tests\TestSupport\Objects\DummyChild;
use Sunhill\Query\QueryParser\QueryNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Query\QueryParser\OrderNode;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Query\QueryParser\AliasNode;
use Sunhill\Parser\Nodes\IntegerNode;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Query', function($manipulator, $expectation)
{
    $test = prepareStorage(DummyChild::class, $this);
    $query_node = new QueryNode();
    $query_node->addStorage('dummychildren');
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
       2
   ],
   'get all with nothing more'=>[
       function($node)
       {
            $node->verb('get');
        },
        function ($result)
        {
            return checkResultArray($result, [13,15]);
        }
   ],
   'first with nothing more'=>[
       function($node)
       {
           $node->verb('first');
        },
        function ($result)
        {
            expect($result->id)->toBeIn([13,15]);
        }],   
    'first with order by dummychildint'=>[
            function($node)
            {
                $order_field = new IdentifierNode('dummychildint');
                $order_field->addChild('alias','b');
                $order_by = new OrderNode();
                $order_by->field($order_field);
                $order_by->direction('asc');
                $node->order($order_by);
                $node->verb('first');
        },
        function ($result)
        {
            expect($result->id)->toBe(13);
        }
        ],
        'first with order by dummyint'=>[
            function($node)
            {
                $alias = $node->addStorage('dummies');
                $order_field = new IdentifierNode('dummyint');
                $order_field->addChild('alias',$alias);
                $order_by = new OrderNode();
                $order_by->field($order_field);
                $order_by->direction('asc');
                $node->order($order_by);
                $node->verb('first');
        },
        function ($result)
        {
            expect($result->id)->toBe(15);
        }
        ],
        'first with order by dummyint reverse order'=>[
            function($node)
            {
                $alias = $node->addStorage('dummies');
                $order_field = new IdentifierNode('dummyint');
                $order_field->addChild('alias',$alias);
                $order_by = new OrderNode();
                $order_by->field($order_field);
                $order_by->direction('desc');
                $node->order($order_by);
                $node->verb('first');
        },
        function ($result)
        {
            expect($result->id)->toBe(13);
        }
        ],
        'get with where with dummychildint'=>[
            function($node)
            {
                $condition = new BinaryNode('=');
                $condition->left( new AliasNode(new IdentifierNode('dummychildint'), 'b') );
                $condition->right( new IntegerNode(979));
                $node->setWhere($condition);
                $node->verb('get');
        },
        function ($result)
        {
            return checkResultArray($result, [15]);
        }
        ],
        'get with where with dummyint'=>[
            function($node)
            {
                $alias = $node->addStorage('dummies');
                $condition = new BinaryNode('>');
                $condition->left( new AliasNode(new IdentifierNode('dummyint'), $alias) );
                $condition->right( new IntegerNode(600));
                $node->setWhere($condition);
                $node->verb('get');
        },
        function ($result)
        {
            return checkResultArray($result, [13,15]);
        }
        ],
        'get with and over two tables'=>[
            function($node)
            {
                $alias = $node->addStorage('dummies');
                $condition_left = new BinaryNode('>');
                $condition_left->left( new AliasNode(new IdentifierNode('dummyint'), $alias) );
                $condition_left->right( new IntegerNode(900));
                
                $condition_right = new BinaryNode('<');
                $condition_right->left( new AliasNode(new IdentifierNode('dummychildint'), 'b') );
                $condition_right->right( new IntegerNode(950));
                
                $condition = new BinaryNode('&&');
                $condition->left($condition_left);
                $condition->right($condition_right);
                $node->setWhere($condition);
                $node->verb('get');
        },
        function($result)
        {
            return checkResultArray($result, [13]);
        }
        ],
        
]);