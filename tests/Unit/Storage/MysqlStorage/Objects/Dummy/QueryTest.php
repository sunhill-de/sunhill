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
        $expectation($result);
    } else {
        expect($result)->toBe($expectation);
    }
})->group('query')->with(
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
                expect($result->id)->toBeIn([1,2,3,4,5,6,13,14,15,16]);
//                return in_array($result->id, [1,2,3,4,5,6,13,14,15,16]);
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
                  expect($result->id)->toBe(1);
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
              expect($result->id)->toBe(3);
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
          ],
          'get with and'=>[
              function($node)
              {
                  $condition_left = new BinaryNode('>');
                  $condition_left->left( new AliasNode(new IdentifierNode('dummyint'), 'b') );
                  $condition_left->right( new IntegerNode(123));
                  
                  $condition_right = new BinaryNode('<');
                  $condition_right->left( new AliasNode(new IdentifierNode('dummyint'), 'b') );
                  $condition_right->right( new IntegerNode(300));

                  $condition = new BinaryNode('&&');
                  $condition->left($condition_left);
                  $condition->right($condition_right);
                  $node->setWhere($condition);
                  $node->verb('get');
          },
              function($result)
              {
                  return checkResultArray($result, [2]);
              }
          ],
          'get with or'=>[
              function($node)
              {
                  $condition_left = new BinaryNode('<');
                  $condition_left->left( new AliasNode(new IdentifierNode('dummyint'), 'b') );
                  $condition_left->right( new IntegerNode(200));
                  
                  $condition_right = new BinaryNode('>');
                  $condition_right->left( new AliasNode(new IdentifierNode('dummyint'), 'b') );
                  $condition_right->right( new IntegerNode(900));
                  
                  $condition = new BinaryNode('||');
                  $condition->left($condition_left);
                  $condition->right($condition_right);
                  $node->setWhere($condition);
                  $node->verb('get');
          },
          function($result)
          {
              return checkResultArray($result, [1,5,13,14,15,16]);
          }
          ],
          'get and and or combined'=>[
              function($node)
              {
                 // (dummyint = 123) || (( dummyint > 900) && ( dummyint < 990))
                 $c_l = new BinaryNode('=');
                 $c_l->left( new AliasNode( new IdentifierNode('dummyint'), 'b'));
                 $c_l->right( new IntegerNode( 123 ));
                 $c_r = new BinaryNode('&&');
                 $c_r_l = new BinaryNode('>');
                 $c_r_l->left( new AliasNode( new IdentifierNode('dummyint'), 'b'));
                 $c_r_l->right( new IntegerNode( 900 ));
                 $c_r_r = new BinaryNode('<');
                 $c_r_r->left( new AliasNode( new IdentifierNode('dummyint'), 'b'));
                 $c_r_r->right( new IntegerNode( 990 ));
                 $c_r->left($c_r_l);
                 $c_r->right($c_r_r);
                 $c = new BinaryNode('||');
                 $c->left($c_l);
                 $c->right($c_r);
                 $node->setWhere($c);
                 $node->verb('get');
          },
              function ($result)
              {
                  return checkResultArray($result, [1,5,14,15,16]);                  
              }
          ]  
      ]);