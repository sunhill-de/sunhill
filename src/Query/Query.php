<?php

/**
 * @file Query.php
 * A base class for other queries
 * Lang en
 * Reviewstatus: 2025-03-25
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Query/QueryTest.php
 * Coverage Unit:
 *
 * @todo implement where with array as parameter (repeat the where statement)
 */

namespace Sunhill\Query;

use Sunhill\Basic\Base;
use Sunhill\Facades\Queries;
use Sunhill\Parser\Executor;
use Sunhill\Parser\Nodes\ArrayNode;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Parser\Nodes\BooleanNode;
use Sunhill\Parser\Nodes\DateNode;
use Sunhill\Parser\Nodes\DateTimeNode;
use Sunhill\Parser\Nodes\FloatNode;
use Sunhill\Parser\Nodes\FunctionNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Parser\Nodes\StringNode;
use Sunhill\Parser\Nodes\TimeNode;
use Sunhill\Parser\Nodes\UnaryNode;
use Sunhill\Query\Exceptions\InvalidOrderException;
use Sunhill\Query\Exceptions\QueryNotWriteableException;
use Sunhill\Query\Exceptions\UnexpectedResultCountException;
use Sunhill\Query\Exceptions\WrongActionException;
use Sunhill\Query\Helpers\MethodSignature;
use Sunhill\Query\QueryParser\Nodes\QueryNode;
use Sunhill\Query\QueryParser\Nodes\OrderNode;

/**
 * The common ancestor for other queries. Defines the interface and some fundamental functions
 * for writing queries. Normally you will use one of the other basic query classes (like DatabaseQuery
 * or ArrayQuery)
 *
 * @author klaus
 */
class Query extends Base
{
    protected ?string $calling_class = null;

    public function setRecordProperty(string $calling_class): static
    {
        $this->calling_class = $calling_class;

        return $this;
    }

    public function getRecordProperty(): ?string
    {
        return $this->calling_class;
    }

    /**
     * Stores the Analzyer to use for this query
     *
     * @var unknown
     */
    protected ?Analyzer $analyzer = null;

    /**
     * Setter for $analyzer
     */
    public function setAnalyzer(Analyzer $analyzer): static
    {
        $this->analyzer = $analyzer;

        return $this;
    }

    /**
     * Getter for Analyzer
     */
    public function getAnalyzer(): ?Analyzer
    {
        return $this->analyzer;
    }

    protected ?Executor $executor = null;

    /**
     * Setter for $executor
     */
    public function setExecutor(Executor $executor): static
    {
        $this->executor = $executor;

        return $this;
    }

    /**
     * Getter for Executor
     */
    public function getExecutor(): ?Executor
    {
        return $this->executor;
    }

    protected array $methods = [];

    /**
     * A static boolean that indicates if this query is readonly
     *
     * @var bool
     */
    protected static $read_only = false;

    protected ?QueryNode $query_node = null;

    /**
     * Initialized the signatures for the offset() method
     *
     * @param  unknown  $node
     */
    private function initializeOffsetSignatures()
    {
        $this->addMethod('offset')->addParameter('integer')->setAction(function (&$node, $offset) {
            $node->offset(new IntegerNode($offset));
        });
        $this->addMethod('offset')->addParameter('callback')->setAction(function (&$node, $offset) {
            $this->offset($offset());
        });
        $this->addMethod('offset')->addParameter('string')->setAction(function (&$node, $offset) {
            $node->offset(Queries::parseQueryString($offset));
        });
        $this->addMethod('offset')->addParameter('node')->setAction(function (&$node, $offset) {
            $node->offset($offset);
        });
    }

    private function initializeLimitSignatures()
    {
        $this->addMethod('limit')->addParameter('integer')->setAction(function (&$node, $limit) {
            $node->limit(new IntegerNode($limit));
        });
        $this->addMethod('limit')->addParameter('callback')->setAction(function (&$node, $limit) {
            $this->limit($limit());
        });
        $this->addMethod('limit')->addParameter('string')->setAction(function (&$node, $limit) {
            $node->limit(Queries::parseQueryString($limit));
        });
        $this->addMethod('limit')->addParameter('node')->setAction(function (&$node, $limit) {
            $node->limit($limit);
        });
    }

    private function initializeOrderSignatures()
    {
        $this->addMethod('order')->addParameter('callback')->setAction(function (&$node, $callback) {
            $this->order($callback());
        });
        $this->addMethod('order')->addParameter('string')->setAction(function (&$node, $order) {
            $parsed = Queries::parseQueryString($order);
            if (is_a($parsed, OrderNode::class)) {
                $node->order($parsed);

                return;
            }
            $new_node = new OrderNode();
            $new_node->field($parsed);
            $new_node->direction('asc');

            $node->order($new_node);
        });
        $this->addMethod('order')->addParameter('stdclass')->setAction(function (&$node, $order) {
            if (! isset($order->field)) {
                throw new InvalidOrderException('There is no field parameter in given stdclass');
            }
            if (! isset($order->direction)) {
                $order->direction = 'asc';
            }
            $new_node = new OrderNode();
            $new_node->field(Queries::parseQueryString($order->field));
            $new_node->direction($order->direction);

            $node->order($new_node);
        });
        $this->addMethod('order')->addParameter('string')->addParameter('string')->setAction(function (&$node, $order, $direction) {
            $new_node = new OrderNode();
            $new_node->field(Queries::parseQueryString($order));
            $direction = strtolower($direction);
            if (($direction !== 'asc') && ($direction !== 'desc')) {
                throw new InvalidOrderException("The direction '$direction' is invalid");
            }
            $new_node->direction($direction);
            $node->order($new_node);
        });
    }

    private function initializeFieldsSignatures()
    {
        $this->addMethod('fields')->addParameter('string')->setAction(function (&$node, $fields) {
            $node->fields(Queries::parseQueryString($fields));
        });
        $this->addMethod('fields')->addParameter('array of string')->setAction(function (&$node, $fields) {
            foreach ($fields as $field) {
                $this->fields($field);
            }
        });
        // alias to be compatible to laravel query builder
        $this->addMethod('select')->addParameter('*')->setAction(function (&$node, $fields) {
            $this->fields($fields);
        });
    }

    private function addWhereCondition(QueryNode &$node, string $connection, $subnode, bool $not = false)
    {
        if ($not) {
            $not_node = new UnaryNode('!');
            $not_node->child($subnode);
            $subnode = $not_node;
        }
        if ($where_node = $node->getWhere()) {
            $connect_node = new BinaryNode($connection);
            $connect_node->left($where_node);
            $connect_node->right($subnode);
            $node->setWhere($connect_node);
        } else {
            $node->setWhere($subnode);
        }
    }

    private function whereWithThreeStrings(Node &$node, string $connection, string $field, string $operator, string $relation, bool $not = false)
    {
        $condition = new BinaryNode($operator);
        $condition->left(Queries::parseQueryString($field));
        $condition->right(Queries::parseQueryString($relation));
        $this->addWhereCondition($node, $connection, $condition, $not);
    }

    private function whereWithTwoStringsAndInteger(Node &$node, string $connection, string $field, string $operator, string $relation, bool $not = false)
    {
        $condition = new BinaryNode($operator);
        $condition->left(Queries::parseQueryString($field));
        $condition->right(new IntegerNode($relation));
        $this->addWhereCondition($node, $connection, $condition, $not);
    }

    private function whereWithTwoStringsAndFloat(Node &$node, string $connection, string $field, string $operator, float $relation, bool $not = false)
    {
        $condition = new BinaryNode($operator);
        $condition->left(Queries::parseQueryString($field));
        $condition->right(new FloatNode($relation));
        $this->addWhereCondition($node, $connection, $condition, $not);
    }

    private function whereWithTwoStringsAndBoolean(Node &$node, string $connection, string $field, string $operator, float $relation, bool $not = false)
    {
        $condition = new BinaryNode($operator);
        $condition->left(Queries::parseQueryString($field));
        $condition->right(new BooleanNode($relation));
        $this->addWhereCondition($node, $connection, $condition, $not);
    }

    private function createElementNode($element): Node
    {
        if (is_int($element)) {
            return new IntegerNode($element);
        }
        if (is_float($element)) {
            return new FloatNode($element);
        }
        if (is_string($element)) {
            return Queries::parseQueryString($element);
        }
        if (is_boolean($element)) {
            return new BooleanNode($element);
        }
    }

    private function buildArrayNodeFromArray(\Traversable|array $array)
    {
        foreach ($array as $element) {
            if (isset($list_node)) {
                $list_node->addElement($this->createElementNode($element));
            } else {
                $list_node = new ArrayNode($this->createElementNode($element));
            }
        }

        return $list_node;
    }

    private function whereWithTwoStringsAndArray(Node &$node, string $connection, string $field, string $operator, \Traversable|array $array, bool $not = false)
    {
        $condition = new BinaryNode($operator);
        $condition->left(Queries::parseQueryString($field));
        $condition->right($this->buildArrayNodeFromArray($array));
        $this->addWhereCondition($node, $connection, $condition, $not);
    }

    private function whereWithOneString(Node &$node, string $connection, string $parameter, bool $not)
    {
        $expression = Queries::parseQueryString($parameter);
        $this->addWhereCondition($node, $connection, $expression, $not);
    }

    private function whereWithOneCallback(Node &$node, string $connection, callable $callback, bool $not)
    {
        $subquery = new Query;
        $callback($subquery);
        $this->addWhereCondition($node, $connection, $subquery->getQueryNode()->getWhere(), $not);
    }

    private function initializeWhereXXXSignatures()
    {
        // Signatures for where()
        $this->addMethod('where')
            ->addParameter('string')->addParameter('string')->addParameter('string')
            ->setAction(function (&$node, $field, $operator, $relation) {
                $this->whereWithThreeStrings($node, '&&', $field, $operator, $relation);
            });
        $this->addMethod('where')->addParameter('string')->addParameter('string')->addParameter('integer')->setAction(function (&$node, $field, $operator, $relation) {
            $this->whereWithTwoStringsAndInteger($node, '&&', $field, $operator, $relation);
        });
        $this->addMethod('where')->addParameter('string')->addParameter('string')->addParameter('float')->setAction(function (&$node, $field, $operator, $relation) {
            $this->whereWithTwoStringsAndFloat($node, '&&', $field, $operator, $relation);
        });
        $this->addMethod('where')->addParameter('string')->addParameter('string')->addParameter('boolean')->setAction(function (&$node, $field, $operator, $relation) {
            $this->whereWithTwoStringsAndBoolean($node, '&&', $field, $operator, $relation);
        });
        $this->addMethod('where')->addParameter('string')->addParameter('string')->setAction(function (&$node, $field, $relation) {
            $this->whereWithThreeStrings($node, '&&', $field, '=', $relation);
        });
        $this->addMethod('where')->addParameter('string')->addParameter('integer')->setAction(function (&$node, $field, $relation) {
            $this->whereWithTwoStringsAndInteger($node, '&&', $field, '=', $relation);
        });
        $this->addMethod('where')->addParameter('string')->addParameter('float')->setAction(function (&$node, $field, $relation) {
            $this->whereWithTwoStringsAndFloat($node, '&&', $field, '=', $relation);
        });
        $this->addMethod('where')->addParameter('string')->addParameter('boolean')->setAction(function (&$node, $field, $relation) {
            $this->whereWithTwoStringsAndBoolean($node, '&&', $field, '=', $relation);
        });
        $this->addMethod('where')->addParameter('string')->setAction(function (&$node, $parameter) {
            $this->whereWithOneString($node, '&&', $parameter, false);
        });
        $this->addMethod('where')->addParameter('callback')->addParameter('*')->addParameter('*')->setAction(function (&$node, $callback, $operator, $relation) {
            $this->where($callback(), $operator, $relation);
        });
        $this->addMethod('where')->addParameter('*')->addParameter('callback')->addParameter('*')->setAction(function (&$node, $field, $callback, $relation) {
            $this->where($field, $callback(), $relation);
        });
        $this->addMethod('where')->addParameter('*')->addParameter('*')->addParameter('callback')->setAction(function (&$node, $field, $operator, $callback) {
            $this->where($field, $operator, $callback());
        });
        $this->addMethod('where')->addParameter('string')->addParameter('string')->addParameter('array')->setAction(function (&$node, $field, $operator, $array) {
            $this->whereWithTwoStringsAndArray($node, '&&', $field, $operator, $array);
        });
        $this->addMethod('where')->addParameter('string')->addParameter('array')->setAction(function (&$node, $field, $array) {
            $this->whereWithTwoStringsAndArray($node, '&&', $field, '=', $array);
        });
        $this->addMethod('where')->addParameter('callback')->setAction(function (&$node, $callback) {
            $this->whereWithOneCallback($node, '&&', $callback, false);
        });
        $this->addMethod('where')->addParameter('array')->setAction(function (&$node, $array) {
            $query = new static;
            foreach ($array as $row) {
                $query->where(...$row);
            }
            $this->addWhereCondition($node, '&&', $query->getQueryNode()->getWhere());
        });
    }

    private function initializeOrWhereXXXSignatures()
    {
        // Signatures for orWhere()
        $this->addMethod('orWhere')
            ->addParameter('string')->addParameter('string')->addParameter('string')
            ->setAction(function (&$node, $field, $operator, $relation) {
                $this->whereWithThreeStrings($node, '||', $field, $operator, $relation);
            });
        $this->addMethod('orWhere')->addParameter('string')->addParameter('string')->addParameter('integer')->setAction(function (&$node, $field, $operator, $relation) {
            $this->whereWithTwoStringsAndInteger($node, '||', $field, $operator, $relation);
        });
        $this->addMethod('orWhere')->addParameter('string')->addParameter('string')->addParameter('float')->setAction(function (&$node, $field, $operator, $relation) {
            $this->whereWithTwoStringsAndFloat($node, '||', $field, $operator, $relation);
        });
        $this->addMethod('orWhere')->addParameter('string')->addParameter('string')->addParameter('boolean')->setAction(function (&$node, $field, $operator, $relation) {
            $this->whereWithTwoStringsAndBoolean($node, '||', $field, $operator, $relation);
        });
        $this->addMethod('orWhere')->addParameter('string')->addParameter('string')->setAction(function (&$node, $field, $relation) {
            $this->whereWithThreeStrings($node, '||', $field, '=', $relation);
        });
        $this->addMethod('orWhere')->addParameter('string')->addParameter('integer')->setAction(function (&$node, $field, $relation) {
            $this->whereWithTwoStringsAndInteger($node, '||', $field, '=', $relation);
        });
        $this->addMethod('orWhere')->addParameter('string')->addParameter('float')->setAction(function (&$node, $field, $relation) {
            $this->whereWithTwoStringsAndFloat($node, '||', $field, '=', $relation);
        });
        $this->addMethod('orWhere')->addParameter('string')->addParameter('boolean')->setAction(function (&$node, $field, $relation) {
            $this->whereWithTwoStringsAndBoolean($node, '||', $field, '=', $relation);
        });
        $this->addMethod('orWhere')->addParameter('string')->setAction(function (&$node, $parameter) {
            $this->whereWithOneString($node, '||', $parameter, false);
        });
        $this->addMethod('orWhere')->addParameter('callback')->addParameter('*')->addParameter('*')->setAction(function (&$node, $callback, $operator, $relation) {
            $this->orWhere($callback(), $operator, $relation);
        });
        $this->addMethod('orWhere')->addParameter('*')->addParameter('callback')->addParameter('*')->setAction(function (&$node, $field, $callback, $relation) {
            $this->orWhere($field, $callback(), $relation);
        });
        $this->addMethod('orWhere')->addParameter('*')->addParameter('*')->addParameter('callback')->setAction(function (&$node, $field, $operator, $callback) {
            $this->orWhere($field, $operator, $callback());
        });
        $this->addMethod('orWhere')->addParameter('string')->addParameter('string')->addParameter('array')->setAction(function (&$node, $field, $operator, $array) {
            $this->whereWithTwoStringsAndArray($node, '||', $field, $operator, $array);
        });
        $this->addMethod('orWhere')->addParameter('string')->addParameter('array')->setAction(function (&$node, $field, $array) {
            $this->whereWithTwoStringsAndArray($node, '||', $field, '=', $array);
        });
        $this->addMethod('orWhere')->addParameter('callback')->setAction(function (&$node, $callback) {
            $this->whereWithOneCallback($node, '||', $callback, false);
        });
        $this->addMethod('orWhere')->addParameter('array')->setAction(function (&$node, $array) {
            $query = new static;
            foreach ($array as $row) {
                $query->where(...$row);
            }
            $this->addWhereCondition($node, '||', $query->getQueryNode()->getWhere());
        });
    }

    private function initializeWhereNotXXXSignatures()
    {
        // Signatures for whereNot()
        $this->addMethod('whereNot')
            ->addParameter('string')->addParameter('string')->addParameter('string')
            ->setAction(function (&$node, $field, $operator, $relation) {
                $this->whereWithThreeStrings($node, '&&', $field, $operator, $relation, true);
            });
        $this->addMethod('whereNot')->addParameter('string')->addParameter('string')->addParameter('integer')->setAction(function (&$node, $field, $operator, $relation) {
            $this->whereWithTwoStringsAndInteger($node, '&&', $field, $operator, $relation, true);
        });
        $this->addMethod('whereNot')->addParameter('string')->addParameter('string')->addParameter('float')->setAction(function (&$node, $field, $operator, $relation) {
            $this->whereWithTwoStringsAndFloat($node, '&&', $field, $operator, $relation, true);
        });
        $this->addMethod('whereNot')->addParameter('string')->addParameter('string')->addParameter('boolean')->setAction(function (&$node, $field, $operator, $relation) {
            $this->whereWithTwoStringsAndBoolean($node, '&&', $field, $operator, $relation, true);
        });
        $this->addMethod('whereNot')->addParameter('string')->addParameter('string')->setAction(function (&$node, $field, $relation) {
            $this->whereWithThreeStrings($node, '&&', $field, '=', $relation, true);
        });
        $this->addMethod('whereNot')->addParameter('string')->addParameter('integer')->setAction(function (&$node, $field, $relation) {
            $this->whereWithTwoStringsAndInteger($node, '&&', $field, '=', $relation, true);
        });
        $this->addMethod('whereNot')->addParameter('string')->addParameter('float')->setAction(function (&$node, $field, $relation) {
            $this->whereWithTwoStringsAndFloat($node, '&&', $field, '=', $relation, true);
        });
        $this->addMethod('whereNot')->addParameter('string')->addParameter('boolean')->setAction(function (&$node, $field, $relation) {
            $this->whereWithTwoStringsAndBoolean($node, '&&', $field, '=', $relation, true);
        });
        $this->addMethod('whereNot')->addParameter('string')->setAction(function (&$node, $parameter) {
            $this->whereWithOneString($node, '&&', $parameter, true);
        });
        $this->addMethod('whereNot')->addParameter('callback')->addParameter('*')->addParameter('*')->setAction(function (&$node, $callback, $operator, $relation) {
            $this->whereNot($callback(), $operator, $relation);
        });
        $this->addMethod('whereNot')->addParameter('*')->addParameter('callback')->addParameter('*')->setAction(function (&$node, $field, $callback, $relation) {
            $this->whereNot($field, $callback(), $relation);
        });
        $this->addMethod('whereNot')->addParameter('*')->addParameter('*')->addParameter('callback')->setAction(function (&$node, $field, $operator, $callback) {
            $this->whereNot($field, $operator, $callback());
        });
        $this->addMethod('whereNot')->addParameter('string')->addParameter('string')->addParameter('array')->setAction(function (&$node, $field, $operator, $array) {
            $this->whereWithTwoStringsAndArray($node, '&&', $field, $operator, $array, true);
        });
        $this->addMethod('whereNot')->addParameter('string')->addParameter('array')->setAction(function (&$node, $field, $array) {
            $this->whereWithTwoStringsAndArray($node, '&&', $field, '=', $array, true);
        });
        $this->addMethod('whereNot')->addParameter('callback')->setAction(function (&$node, $callback) {
            $this->whereWithOneCallback($node, '&&', $callback, true);
        });
        $this->addMethod('whereNot')->addParameter('array')->setAction(function (&$node, $array) {
            $query = new static;
            foreach ($array as $row) {
                $query->where(...$row);
            }
            $this->addWhereCondition($node, '&&', $query->getQueryNode()->getWhere(), true);
        });
    }

    private function initializeOrWhereNotXXXSignatures()
    {
        // Signatures for orWhereNot()
        $this->addMethod('orWhereNot')
            ->addParameter('string')->addParameter('string')->addParameter('string')
            ->setAction(function (&$node, $field, $operator, $relation) {
                $this->whereWithThreeStrings($node, '||', $field, $operator, $relation, true);
            });
        $this->addMethod('orWhereNot')->addParameter('string')->addParameter('string')->addParameter('integer')->setAction(function (&$node, $field, $operator, $relation) {
            $this->whereWithTwoStringsAndInteger($node, '||', $field, $operator, $relation, true);
        });
        $this->addMethod('orWhereNot')->addParameter('string')->addParameter('string')->addParameter('float')->setAction(function (&$node, $field, $operator, $relation) {
            $this->whereWithTwoStringsAndFloat($node, '||', $field, $operator, $relation, true);
        });
        $this->addMethod('orWhereNot')->addParameter('string')->addParameter('string')->addParameter('boolean')->setAction(function (&$node, $field, $operator, $relation) {
            $this->whereWithTwoStringsAndBoolean($node, '||', $field, $operator, $relation, true);
        });
        $this->addMethod('orWhereNot')->addParameter('string')->addParameter('string')->setAction(function (&$node, $field, $relation) {
            $this->whereWithThreeStrings($node, '||', $field, '=', $relation, true);
        });
        $this->addMethod('orWhereNot')->addParameter('string')->addParameter('integer')->setAction(function (&$node, $field, $relation) {
            $this->whereWithTwoStringsAndInteger($node, '||', $field, '=', $relation, true);
        });
        $this->addMethod('orWhereNot')->addParameter('string')->addParameter('float')->setAction(function (&$node, $field, $relation) {
            $this->whereWithTwoStringsAndFloat($node, '||', $field, '=', $relation, true);
        });
        $this->addMethod('orWhereNot')->addParameter('string')->addParameter('boolean')->setAction(function (&$node, $field, $relation) {
            $this->whereWithTwoStringsAndBoolean($node, '||', $field, '=', $relation, true);
        });
        $this->addMethod('orWhereNot')->addParameter('string')->setAction(function (&$node, $parameter) {
            $this->whereWithOneString($node, '||', $parameter, true);
        });
        $this->addMethod('orWhereNot')->addParameter('callback')->addParameter('*')->addParameter('*')->setAction(function (&$node, $callback, $operator, $relation) {
            $this->orWhereNot($callback(), $operator, $relation);
        });
        $this->addMethod('orWhereNot')->addParameter('*')->addParameter('callback')->addParameter('*')->setAction(function (&$node, $field, $callback, $relation) {
            $this->orWhereNot($field, $callback(), $relation);
        });
        $this->addMethod('orWhereNot')->addParameter('*')->addParameter('*')->addParameter('callback')->setAction(function (&$node, $field, $operator, $callback) {
            $this->orWhereNot($field, $operator, $callback());
        });
        $this->addMethod('orWhereNot')->addParameter('string')->addParameter('string')->addParameter('array')->setAction(function (&$node, $field, $operator, $array) {
            $this->whereWithTwoStringsAndArray($node, '||', $field, $operator, $array, true);
        });
        $this->addMethod('orWhereNot')->addParameter('string')->addParameter('array')->setAction(function (&$node, $field, $array) {
            $this->whereWithTwoStringsAndArray($node, '||', $field, '=', $array, true);
        });
        $this->addMethod('orWhereNot')->addParameter('callback')->setAction(function (&$node, $callback) {
            $this->whereWithOneCallback($node, '||', $callback, true);
        });
        $this->addMethod('orWhereNot')->addParameter('array')->setAction(function (&$node, $array) {
            $query = new static;
            foreach ($array as $row) {
                $query->where(...$row);
            }
            $this->addWhereCondition($node, '||', $query->getQueryNode()->getWhere(), true);
        });

    }

    private function initializeWhereInSignatures()
    {
        $this->addMethod('whereIn')->addParameter('string')->addParameter('array')->setAction(function (&$node, $field, $array) {
            $this->where($field, 'in', $array);
        });
        $this->addMethod('orWhereIn')->addParameter('string')->addParameter('array')->setAction(function (&$node, $field, $array) {
            $this->orWhere($field, 'in', $array);
        });
        $this->addMethod('whereNotIn')->addParameter('string')->addParameter('array')->setAction(function (&$node, $field, $array) {
            $this->whereNot($field, 'in', $array);
        });
        $this->addMethod('orWhereNotIn')->addParameter('string')->addParameter('array')->setAction(function (&$node, $field, $array) {
            $this->orWhereNot($field, 'in', $array);
        });
    }

    private function initializeWhereAnyAllNoneSignatures()
    {
        $this->addMethod('whereAny')->addParameters(['array', 'string', '*'])->setAction(function (&$node, $fields, $operator, $relation) {
            $query = new static;
            foreach ($fields as $field) {
                $query->orWhere($field, $operator, $relation);
            }
            $this->addWhereCondition($node, '&&', $query->getQueryNode()->getWhere(), false);
        });
        $this->addMethod('orWhereAny')->addParameters(['array', 'string', '*'])->setAction(function (&$node, $fields, $operator, $relation) {
            $query = new static;
            foreach ($fields as $field) {
                $query->orWhere($field, $operator, $relation);
            }
            $this->addWhereCondition($node, '||', $query->getQueryNode()->getWhere(), false);
        });
        $this->addMethod('whereNotAny')->addParameters(['array', 'string', '*'])->setAction(function (&$node, $fields, $operator, $relation) {
            $query = new static;
            foreach ($fields as $field) {
                $query->orWhere($field, $operator, $relation);
            }
            $this->addWhereCondition($node, '&&', $query->getQueryNode()->getWhere(), true);
        });
        $this->addMethod('orWhereNotAny')->addParameters(['array', 'string', '*'])->setAction(function (&$node, $fields, $operator, $relation) {
            $query = new static;
            foreach ($fields as $field) {
                $query->orWhere($field, $operator, $relation);
            }
            $this->addWhereCondition($node, '||', $query->getQueryNode()->getWhere(), true);
        });

        $this->addMethod('whereAll')->addParameters(['array', 'string', '*'])->setAction(function (&$node, $fields, $operator, $relation) {
            $query = new static;
            foreach ($fields as $field) {
                $query->where($field, $operator, $relation);
            }
            $this->addWhereCondition($node, '&&', $query->getQueryNode()->getWhere(), false);
        });
        $this->addMethod('orWhereAll')->addParameters(['array', 'string', '*'])->setAction(function (&$node, $fields, $operator, $relation) {
            $query = new static;
            foreach ($fields as $field) {
                $query->where($field, $operator, $relation);
            }
            $this->addWhereCondition($node, '||', $query->getQueryNode()->getWhere(), false);
        });
        $this->addMethod('whereNotAll')->addParameters(['array', 'string', '*'])->setAction(function (&$node, $fields, $operator, $relation) {
            $query = new static;
            foreach ($fields as $field) {
                $query->where($field, $operator, $relation);
            }
            $this->addWhereCondition($node, '&&', $query->getQueryNode()->getWhere(), true);
        });
        $this->addMethod('orWhereNotAll')->addParameters(['array', 'string', '*'])->setAction(function (&$node, $fields, $operator, $relation) {
            $query = new static;
            foreach ($fields as $field) {
                $query->where($field, $operator, $relation);
            }
            $this->addWhereCondition($node, '||', $query->getQueryNode()->getWhere(), true);
        });

        $this->addMethod('whereNone')->addParameters(['array', 'string', '*'])->setAction(function (&$node, $fields, $operator, $relation) {
            $query = new static;
            foreach ($fields as $field) {
                $query->whereNot($field, $operator, $relation);
            }
            $this->addWhereCondition($node, '&&', $query->getQueryNode()->getWhere(), false);
        });
        $this->addMethod('orWhereNone')->addParameters(['array', 'string', '*'])->setAction(function (&$node, $fields, $operator, $relation) {
            $query = new static;
            foreach ($fields as $field) {
                $query->whereNot($field, $operator, $relation);
            }
            $this->addWhereCondition($node, '||', $query->getQueryNode()->getWhere(), false);
        });
        $this->addMethod('whereNotNone')->addParameters(['array', 'string', '*'])->setAction(function (&$node, $fields, $operator, $relation) {
            $query = new static;
            foreach ($fields as $field) {
                $query->whereNot($field, $operator, $relation);
            }
            $this->addWhereCondition($node, '&&', $query->getQueryNode()->getWhere(), true);
        });
        $this->addMethod('orWhereNotNone')->addParameters(['array', 'string', '*'])->setAction(function (&$node, $fields, $operator, $relation) {
            $query = new static;
            foreach ($fields as $field) {
                $query->whereNot($field, $operator, $relation);
            }
            $this->addWhereCondition($node, '||', $query->getQueryNode()->getWhere(), true);
        });

    }

    private function initializeWhereHasSignatures()
    {
        $this->addMethod('whereHasAny')->addParameters(['string', 'array'])->setAction(function (&$node, $field, $values) {
            $this->where($field, 'has_any', $values);
        });
        $this->addMethod('orWhereHasAny')->addParameters(['string', 'array'])->setAction(function (&$node, $field, $values) {
            $this->orWhere($field, 'has_any', $values);
        });
        $this->addMethod('whereNotHasAny')->addParameters(['string', 'array'])->setAction(function (&$node, $field, $values) {
            $this->whereNot($field, 'has_any', $values);
        });
        $this->addMethod('orWhereNotHasAny')->addParameters(['string', 'array'])->setAction(function (&$node, $field, $values) {
            $this->orWhereNot($field, 'has_any', $values);
        });

        $this->addMethod('whereHasAll')->addParameters(['string', 'array'])->setAction(function (&$node, $field, $values) {
            $this->where($field, 'has_all', $values);
        });
        $this->addMethod('orWhereHasAll')->addParameters(['string', 'array'])->setAction(function (&$node, $field, $values) {
            $this->orWhere($field, 'has_all', $values);
        });
        $this->addMethod('whereNotHasAll')->addParameters(['string', 'array'])->setAction(function (&$node, $field, $values) {
            $this->whereNot($field, 'has_all', $values);
        });
        $this->addMethod('orWhereNotHasAll')->addParameters(['string', 'array'])->setAction(function (&$node, $field, $values) {
            $this->orWhereNot($field, 'has_all', $values);
        });

        $this->addMethod('whereHasNone')->addParameters(['string', 'array'])->setAction(function (&$node, $field, $values) {
            $this->where($field, 'has_none', $values);
        });
        $this->addMethod('orWhereHasNone')->addParameters(['string', 'array'])->setAction(function (&$node, $field, $values) {
            $this->orWhere($field, 'has_none', $values);
        });
        $this->addMethod('whereNotHasNone')->addParameters(['string', 'array'])->setAction(function (&$node, $field, $values) {
            $this->whereNot($field, 'has_none', $values);
        });
        $this->addMethod('orWhereNotHasNone')->addParameters(['string', 'array'])->setAction(function (&$node, $field, $values) {
            $this->orWhereNot($field, 'has_none', $values);
        });
    }

    private function initializeWhereColumnSignatures()
    {
        $this->addMethod('whereColumn')->addParameters(['string', 'string'])->setAction(function (&$node, $field1, $field2) {
            $this->where($field1, '=', $field2);
        });
        $this->addMethod('orWhereColumn')->addParameters(['string', 'string'])->setAction(function (&$node, $field1, $field2) {
            $this->orWhere($field1, '=', $field2);
        });
        $this->addMethod('whereNotColumn')->addParameters(['string', 'string'])->setAction(function (&$node, $field1, $field2) {
            $this->whereNot($field1, '=', $field2);
        });
        $this->addMethod('orWhereNotColumn')->addParameters(['string', 'string'])->setAction(function (&$node, $field1, $field2) {
            $this->orWhereNot($field1, '=', $field2);
        });
    }

    private function createFunctionWithArgument(string $function_name, $argument): Node
    {
        $function_node = new FunctionNode($function_name);
        $function_node->arguments($this->createElementNode($argument));

        return $function_node;
    }

    private function initializeWhereDateSignatures()
    {
        $this->addMethod('whereDate')->addParameters(['string', 'string'])->setAction(function (&$node, $field, $date) {
            $this->where("date($field)", '=', $date);
        });
        $this->addMethod('orWhereDate')->addParameters(['string', 'string'])->setAction(function (&$node, $field, $date) {
            $this->orWhere("date($field)", '=', $date);
        });
        $this->addMethod('whereNotDate')->addParameters(['string', 'string'])->setAction(function (&$node, $field, $date) {
            $this->whereNot("date($field)", '=', $date);
        });
        $this->addMethod('orWhereNotDate')->addParameters(['string', 'string'])->setAction(function (&$node, $field, $date) {
            $this->orWhereNot("date($field)", '=', $date);
        });
        $this->addMethod('whereMonth')->addParameters(['string', 'string|integer'])->setAction(function (&$node, $field, $date) {
            $this->where("month($field)", '=', $date);
        });
        $this->addMethod('orWhereMonth')->addParameters(['string', 'string|integer'])->setAction(function (&$node, $field, $date) {
            $this->orWhere("month($field)", '=', $date);
        });
        $this->addMethod('whereNotMonth')->addParameters(['string', 'string|integer'])->setAction(function (&$node, $field, $date) {
            $this->whereNot("month($field)", '=', $date);
        });
        $this->addMethod('orWhereNotMonth')->addParameters(['string', 'string|integer'])->setAction(function (&$node, $field, $date) {
            $this->orWhereNot("month($field)", '=', $date);
        });
        $this->addMethod('whereDay')->addParameters(['string', 'string|integer'])->setAction(function (&$node, $field, $date) {
            $this->where("day($field)", '=', $date);
        });
        $this->addMethod('orWhereDay')->addParameters(['string', 'string|integer'])->setAction(function (&$node, $field, $date) {
            $this->orWhere("day($field)", '=', $date);
        });
        $this->addMethod('whereNotDay')->addParameters(['string', 'string|integer'])->setAction(function (&$node, $field, $date) {
            $this->whereNot("day($field)", '=', $date);
        });
        $this->addMethod('orWhereNotDay')->addParameters(['string', 'string|integer'])->setAction(function (&$node, $field, $date) {
            $this->orWhereNot("day($field)", '=', $date);
        });
        $this->addMethod('whereYear')->addParameters(['string', 'string|integer'])->setAction(function (&$node, $field, $date) {
            $this->where("year($field)", '=', $date);
        });
        $this->addMethod('orWhereYear')->addParameters(['string', 'string|integer'])->setAction(function (&$node, $field, $date) {
            $this->orWhere("year($field)", '=', $date);
        });
        $this->addMethod('whereNotYear')->addParameters(['string', 'string|integer'])->setAction(function (&$node, $field, $date) {
            $this->whereNot("year($field)", '=', $date);
        });
        $this->addMethod('orWhereNotYear')->addParameters(['string', 'string|integer'])->setAction(function (&$node, $field, $date) {
            $this->orWhereNot("year($field)", '=', $date);
        });
        $this->addMethod('whereTime')->addParameters(['string', 'string'])->setAction(function (&$node, $field, $date) {
            $this->where("time($field)", '=', $date);
        });
        $this->addMethod('orWhereTime')->addParameters(['string', 'string'])->setAction(function (&$node, $field, $date) {
            $this->orWhere("time($field)", '=', $date);
        });
        $this->addMethod('whereNotTime')->addParameters(['string', 'string'])->setAction(function (&$node, $field, $date) {
            $this->whereNot("time($field)", '=', $date);
        });
        $this->addMethod('orWhereNotTime')->addParameters(['string', 'string'])->setAction(function (&$node, $field, $date) {
            $this->orWhereNot("time($field)", '=', $date);
        });

        $this->addMethod('wherePast')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->where($field, '<', 'now()');
        });
        $this->addMethod('orWherePast')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhere($field, '<', 'now()');
        });
        $this->addMethod('whereNotPast')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->whereNot($field, '<', 'now()');
        });
        $this->addMethod('orWhereNotPast')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhereNot($field, '<', 'now()');
        });

        $this->addMethod('whereFuture')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->where($field, '>', 'now()');
        });
        $this->addMethod('orWhereFuture')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhere($field, '>', 'now()');
        });
        $this->addMethod('whereNotFuture')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->whereNot($field, '>', 'now()');
        });
        $this->addMethod('orWhereNotFuture')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhereNot($field, '>', 'now()');
        });
        $this->addMethod('whereNowOrPast')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->where($field, '<=', 'now()');
        });
        $this->addMethod('orWhereNowOrPast')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhere($field, '<=', 'now()');
        });
        $this->addMethod('whereNotNowOrPast')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->whereNot($field, '<=', 'now()');
        });
        $this->addMethod('orWhereNotNowOrPast')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhereNot($field, '<=', 'now()');
        });
        $this->addMethod('whereNowOrFuture')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->where($field, '>=', 'now()');
        });
        $this->addMethod('orWhereNowOrFuture')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhere($field, '>=', 'now()');
        });
        $this->addMethod('whereNotNowOrFuture')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->whereNot($field, '>=', 'now()');
        });
        $this->addMethod('orWhereNotNowOrFuture')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhereNot($field, '>=', 'now()');
        });
        $this->addMethod('whereToday')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->where("date($field)", '=', 'date(now())');
        });
        $this->addMethod('orWhereToday')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhere("date($field)", '=', 'date(now())');
        });
        $this->addMethod('whereNotToday')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->whereNot("date($field)", '=', 'date(now())');
        });
        $this->addMethod('orWhereNotToday')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhereNot("date($field)", '=', 'date(now())');
        });
        $this->addMethod('whereBeforeToday')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->where("date($field)", '<', 'date(now())');
        });
        $this->addMethod('orWhereBeforeToday')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhere("date($field)", '<', 'date(now())');
        });
        $this->addMethod('whereNotBeforeToday')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->whereNot("date($field)", '<', 'date(now())');
        });
        $this->addMethod('orWhereNotBeforeToday')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhereNot("date($field)", '<', 'date(now())');
        });
        $this->addMethod('whereAfterToday')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->where("date($field)", '>', 'date(now())');
        });
        $this->addMethod('orWhereAfterToday')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhere("date($field)", '>', 'date(now())');
        });
        $this->addMethod('whereNotAfterToday')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->whereNot("date($field)", '>', 'date(now())');
        });
        $this->addMethod('orWhereNotAfterToday')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhereNot("date($field)", '>', 'date(now())');
        });
        $this->addMethod('whereTodayOrBefore')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->where("date($field)", '<=', 'date(now())');
        });
        $this->addMethod('orWhereTodayOrBefore')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhere("date($field)", '<=', 'date(now())');
        });
        $this->addMethod('whereNotTodayOrBefore')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->whereNot("date($field)", '<=', 'date(now())');
        });
        $this->addMethod('orWhereNotTodayOrBefore')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhereNot("date($field)", '<=', 'date(now())');
        });
        $this->addMethod('whereTodayOrAfter')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->where("date($field)", '>=', 'date(now())');
        });
        $this->addMethod('orWhereTodayOrAfter')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhere("date($field)", '>=', 'date(now())');
        });
        $this->addMethod('whereNotTodayOrAfter')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->whereNot("date($field)", '>=', 'date(now())');
        });
        $this->addMethod('orWhereNotTodayOrAfter')->addParameters(['string'])->setAction(function (&$node, $field) {
            $this->orWhereNot("date($field)", '>=', 'date(now())');
        });

    }

    private function initializeOtherWheres()
    {
        $this->addMethod('whereNull')->addParameter('*')->setAction(function (&$node, $field) {
            $this->where($field, 'is_null', 0);
        });
        $this->addMethod('orWhereNull')->addParameter('*')->setAction(function (&$node, $field) {
            $this->orWhere($field, 'is_null', 0);
        });
        $this->addMethod('whereNotNull')->addParameter('*')->setAction(function (&$node, $field) {
            $this->where($field, 'is_not_null', 0);
        });
        $this->addMethod('orWhereNotNull')->addParameter('*')->setAction(function (&$node, $field) {
            $this->orWhere($field, 'is_not_null', 0);
        });
        $this->addMethod('whereBetween')->addParameters(['string|integer|float', 'array'])->setAction(function (&$node, $field, $range) {
            $subquery = new Query;
            $subquery->where($field, '>', $range[0])->where($field, '<', $range[1]);
            $this->addWhereCondition($node, '&&', $subquery->getQueryNode()->getWhere());
        });
        $this->addMethod('orWhereBetween')->addParameters(['string|integer|float', 'array'])->setAction(function (&$node, $field, $range) {
            $subquery = new Query;
            $subquery->where($field, '>', $range[0])->where($field, '<', $range[1]);
            $this->addWhereCondition($node, '||', $subquery->getQueryNode()->getWhere());
        });
        $this->addMethod('whereNotBetween')->addParameters(['string|integer|float', 'array'])->setAction(function (&$node, $field, $range) {
            $subquery = new Query;
            $subquery->where($field, '>', $range[0])->where($field, '<', $range[1]);
            $this->addWhereCondition($node, '&&', $subquery->getQueryNode()->getWhere(), true);
        });
        $this->addMethod('orWhereNotBetween')->addParameters(['string|integer|float', 'array'])->setAction(function (&$node, $field, $range) {
            $subquery = new Query;
            $subquery->where($field, '>', $range[0])->where($field, '<', $range[1]);
            $this->addWhereCondition($node, '||', $subquery->getQueryNode()->getWhere(), true);
        });
        $this->addMethod('whereLike')->addParameters(['string', 'string'])->setAction(function (&$node, $field, $pattern) {
            $this->where($field, 'like', $pattern);
        });
        $this->addMethod('orWhereLike')->addParameters(['string', 'string'])->setAction(function (&$node, $field, $pattern) {
            $this->orWhere($field, 'like', $pattern);
        });
        $this->addMethod('whereNotLike')->addParameters(['string', 'string'])->setAction(function (&$node, $field, $pattern) {
            $this->whereNot($field, 'like', $pattern);
        });
        $this->addMethod('orWhereNotLike')->addParameters(['string', 'string'])->setAction(function (&$node, $field, $pattern) {
            $this->orWhereNot($field, 'like', $pattern);
        });
    }

    private function initializeWhereSignatures()
    {
        $this->initializeWhereXXXSignatures();
        $this->initializeOrWhereXXXSignatures();
        $this->initializeWhereNotXXXSignatures();
        $this->initializeOrWhereNotXXXSignatures();
        $this->initializeWhereInSignatures();
        $this->initializeWhereAnyAllNoneSignatures();
        $this->initializeWhereHasSignatures();
        $this->initializeWhereColumnSignatures();
        $this->initializeWhereDateSignatures();
        $this->initializeOtherWheres();
    }

    public function __construct()
    {
        $this->query_node = new QueryNode;
        $this->initializeOffsetSignatures();
        $this->initializeLimitSignatures();
        $this->initializeOrderSignatures();
        $this->initializeFieldsSignatures();
        $this->initializeWhereSignatures();
    }

    /**
     * Mainly for internal debugging purposes. Returns the current query node
     */
    public function getQueryNode(): QueryNode
    {
        return $this->query_node;
    }

    /**
     * Checks if this query is readonly. If yes it throws an exception
     */
    protected function checkForReadonly(string $feature)
    {
        if (static::$read_only) {
            throw new QueryNotWriteableException("The feature '$feature' is not avaiable, because this query is read-only");
        }
    }

    public function addMethod(string $name): MethodSignature
    {
        $signature = new MethodSignature;
        if (isset($this->methods[$name])) {
            $this->methods[$name][] = $signature;
        } else {
            $this->methods[$name] = [$signature];
        }

        return $signature;
    }

    protected function performAction($action, array $arguments)
    {
        if (is_callable($action)) {
            return $action($this->query_node, ...$arguments);
        }
        throw new WrongActionException('The action is invalid');
    }

    /**
     * Catchall for method calls that checks if the method starts with where, whereNot, orWhere or orWhereNot
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (! isset($this->methods[$name])) {
            throw new \Exception("Method '$name' not found");
        }
        foreach ($this->methods[$name] as $signature) {
            if ($signature->matches($arguments)) {
                $this->performAction($signature->getAction(), $arguments);

                return $this;
            }
        }
        throw new \Exception("Method '$name' with this signature not found");
    }

    // Other statements

    /**
     * First checks the query (if it is valid), prepares it and then Calls the query executor and returns its result
     *
     * @param  string  $finalizer.  This could be any of:
     *                              - "first": Returns the id that matches the conditions
     *                              - "get": Returns all ids that macthes the conditions
     */
    protected function executeQuery(string $finalizer, array $params = [])
    {
        $this->query_node->verb($finalizer);

        //       $this->analyzer->analyze($this->query_node);
        return $this->executor->execute($this->query_node, $params);
    }

    public function union($other_query): static {}

    public function unionAll($other_query): static {}

    // ============================================ Finalizing methods =============================================================
    private function createRecord($id)
    {
        $class = $this->calling_class;
        $result = new $class;
        $result->load($id);

        return $result;
    }

    /**
     * Returns the first record that matches the conditionsgit
     */
    public function first()
    {
        if (empty($id = $this->firstID())) {
            return null;
        }

        return $this->createRecord($id);
    }

    /**
     * Returns the first entry that matches the conditions or throws an exception if none exists
     */
    public function firstOrFail()
    {
        $id = $this->firstIDOrFail();

        return $this->createRecord($id);
    }

    /**
     * Returns the first id that matches the conditions or throws an exception if none exists
     */
    public function firstIDOrFail()
    {
        $result = $this->firstID();
        if (empty($result)) {
            throw new UnexpectedResultCountException('At least one result is expected for firstOrFail(), none returned');
        }

        return $result;
    }

    /**
     * Returns the first id that matches the conditions
     */
    public function firstID()
    {
        return $this->executeQuery('first');
    }

    /**
     * Returns only the value of the given row(s)
     */
    public function value($column) {}

    /**
     * Returns that record with id given id
     */
    public function find($id) {}

    /**
     * Returns only the given fields in a collection of stdClass of all items that match the conditions
     */
    public function pluck(...$fields) {}

    /**
     * Collects $number of entries and passes them to callback
     */
    public function chunk(int $number, callable $callback) {}

    public function chunkByID(int $number, callable $callback) {}

    /**
     * Returns the count of records that matches the conditions
     */
    public function count(): int {}

    /**
     * Returns the highest value of the given field
     */
    public function max($field) {}

    /**
     * Returns the lowest value of the given field
     */
    public function min($field) {}

    /**
     * Calculates the average of the given field (when it is numeric)
     */
    public function avg(string $field): int|float {}

    /**
     * Sums up all values of the given field (when it is numeric)
     */
    public function sum($field): int|float {}

    /**
     * Returns true when at least one dataset matches the given condition
     */
    public function exists(): bool {}

    /**
     * Returns true when no dataset matches the given condition
     */
    public function doesntExist(): bool {}

    /**
     * Returns all record that matches the conditions
     */
    public function get() {}

    /**
     * Returns a list of ids that matches the conditions
     */
    public function getIDs() {}

    /**
     * Expects excactly one result and returns it. If more or less it raises an exception
     */
    public function only() {}

    /**
     * Expects excactly one result and returns its id. If more or less it raises an exception
     */
    public function onlyID() {}

    /**
     * Deletes all records that macthes the conditions
     */
    public function delete()
    {
        $this->checkForReadonly('delete');
    }

    /**
     * Inserts a record into the set
     *
     * @param  unknown  $data_set
     */
    public function insert($data_set)
    {
        $this->checkForReadonly('insert');
    }

    /**
     * Update all records that match the conditions
     *
     * @param  unknown  $data_set
     */
    public function update($data_set)
    {
        $this->checkForReadonly('update');
    }

    /**
     * Inserts or updates a record depending of $condition
     *
     * @param  unknown  $condition
     * @param  unknown  $data_set
     */
    public function upsert($condition, $data_set)
    {
        $this->checkForReadlnly('upsert');
    }

    /**
     * Creates a identifier node with the name $node. The type is optional.
     */
    public static function identifier(string $name, ?string $type = null): IdentifierNode
    {
        $result = new IdentifierNode($name);
        if (! is_null($type)) {
            $result->setDatatype($type);
        }

        return $result;
    }

    public static function funct(string $name, $arguments = null): FunctionNode
    {
        $result = new FunctionNode($name);
        if (is_a($arguments, ArrayNode::class) || is_a($arguments, Node::class)) {
            $result->arguments($arguments);
        } elseif (is_array($arguments)) {
            $args = new ArrayNode;
            foreach ($arguments as $argument) {
                $args->addElement($argument);
            }
            $result->arguments($args);
        }

        return $result;
    }

    /**
     * Creates an integer constant node with value $value
     */
    public static function integerConstant(int $value): IntegerNode
    {
        $result = new IntegerNode($value);

        return $result;
    }

    /**
     * Creates a string constant node with the given value
     */
    public static function stringConstant(string $value): StringNode
    {
        $result = new StringNode($value);

        return $result;
    }

    /**
     * Creates a float constant node with the given value
     */
    public static function floatConstant(float $value): FloatNode
    {
        $result = new FloatNode($value);

        return $result;
    }

    public static function dateConstant(string $date): DateNode
    {
        $result = new DateNode($date);

        return $result;
    }

    public static function datetimeConstant(string $datetime): DateTimeNode
    {
        $result = new DateTimeNode($datetime);

        return $result;
    }

    public static function timeConstant(string $time): TimeNode
    {
        $result = new TimeNode($time);

        return $result;
    }

    public static function booleanConstant(bool $value): BooleanNode
    {
        $result = new BooleanNode($value);

        return $result;
    }

    /**
     * Creates an binary operator node
     */
    public static function binaryOperator(string $operator, Node $left, Node $right): BinaryNode
    {
        $result = new BinaryNode($operator);
        $result->left($left);
        $result->right($right);

        return $result;
    }

    /**
     * Creates a unary operator node
     */
    public static function unaryOperator(string $operator, Node $child): UnaryNode
    {
        $result = new UnaryNode($operator);
        $result->child($child);

        return $result;
    }

    public static function array($elements = null): ArrayNode
    {
        $result = new ArrayNode(null);
        if (is_a($elements, Node::class)) {
            $result->addElement($elements);
        }
        if (is_array($elements)) {
            foreach ($elements as $element) {
                $result->addElement($element);
            }
        }

        return $result;
    }
}
