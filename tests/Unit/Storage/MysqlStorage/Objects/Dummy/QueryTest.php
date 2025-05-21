<?php

use Sunhill\Tests\SunhillDatabaseTestCase;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tests\TestSupport\Objects\Dummy;
use Sunhill\Query\QueryParser\QueryNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Query\QueryParser\OrderNode;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Query\QueryParser\AliasNode;

uses(SunhillDatabaseTestCase::class);

require_once(dirname(__FILE__).'/../ObjectHelpers.php');

test('Query', function($manipulator, $expectation)
{
    $test = prepareStorage(Dummy::class, $this);
    $query_node = new QueryNode();
    $query_node->addStorage('dummies');
    $manipulator($query_node);
    $result = $test->executeQuery($query_node);
    if (is_callable($expectation)) {
        expect($expectation($result))->toBe(true);
    } else {
        expect($result)->toBe($expectation);
    }
})->with(
    [
        'simple count'=>[function($node) { $node->verb('count'); }, 10],
        'get all with nothing more'=>[
            function($node) 
            { 
                $node->verb('get'); 
            }, 
            function ($result)
            {
                return checkResultArray($result, [1,2,3,4,5,6,13,14,15,16]);
            }
            ],
        'first with nothing more'=>[
            function($node) 
            { 
                $node->verb('first'); 
            }, 
            function ($result)
            {
                return in_array($result->id, [1,2,3,4,5,6,13,14,15,16]);
            }],
          'first with order'=>[
              function($node)
              {
                  $order_field = new IdentifierNode('id');
                  $order_field->addChild('alias','a');
                  $order_by = new OrderNode();
                  $order_by->field($order_field);
                  $order_by->direction('asc');
                  $node->order($order_by);
                  $node->verb('first');
              },
              function ($result)
              {
                  return $result->id == 1;
              }
          ],
          'first with order and offset'=>[
              function($node)
              {
                  $order_field = new IdentifierNode('id');
                  $order_field->addChild('alias','a');
                  $order_by = new OrderNode();
                  $order_by->field($order_field);
                  $order_by->direction('asc');
                  $node->order($order_by);
                  $node->offset(new IntegerNode(2));
                  $node->verb('first');
          },
          function ($result)
          {
              return $result->id == 3;
          }
          ],
          'get with order and limit'=>[
              function($node)
              {
                  $order_field = new IdentifierNode('id');
                  $order_field->addChild('alias','a');
                  $order_by = new OrderNode();
                  $order_by->field($order_field);
                  $order_by->direction('asc');
                  $node->order($order_by);
                  $node->limit(new IntegerNode(4));
                  $node->verb('get');                  
              },
              function($result)
              {
                  return checkResultArray($result, [1,2,3,4]);
              }
          ],
          'get with where'=>[
              function($node)
              {
                    $condition = new BinaryNode('=');
                    $condition->left( new AliasNode(new IdentifierNode('dummyint'), 'b') );
                    $condition->right( new IntegerNode(123));
                    $node->setWhere($condition);
                    $node->verb('get');
              },
              function ($result)
              {
                  return checkResultArray($result, [1,5]);
              }
          ]
//        'get all with where'=>[function($node) { $node->verb('get'); $node->},]
    ]);