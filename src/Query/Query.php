<?php
/**
 * @file Query.php
 * A base class for other queries
 * Lang en
 * Reviewstatus: 2025-03-25
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Query/QueryTest.php
 * Coverage: 
 */

namespace Sunhill\Query;

use Illuminate\Support\Collection;
use Sunhill\Query\Exceptions\InvalidOrderException;
use Sunhill\Query\Exceptions\NoResultException;
use Sunhill\Query\Exceptions\UnknownFieldException;
use Sunhill\Query\Exceptions\TooManyResultsException;
use Sunhill\Query\Exceptions\QueryNotWriteableException;
use Sunhill\Query\Exceptions\InvalidStatementException;
use Illuminate\Support\Str;
use Sunhill\Query\Exceptions\UnexpectedResultCountException;
use Sunhill\Facades\Properties;
use Sunhill\Basic\Base;
use Sunhill\Query\Helpers\MethodSignature;
use Sunhill\Query\Helpers\QueryNode;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Query\Exceptions\WrongActionException;
use Sunhill\Parser\Nodes\IntegerNode;

/**
 * The common ancestor for other queries. Defines the interface and some fundamental functions
 * for writing queries. Normally you will use one of the other basic query classes (like DatabaseQuery 
 * or ArrayQuery) 
 * 
 * @author klaus
 *
 */
class Query extends Base
{
    
    protected array $methods = [];
    
    /**
     * A static boolean that indicates if this query is readonly
     * @var boolean
     */
    protected static $read_only = false;
    
    protected ?QueryNode $query_node = null;
    
    public function __construct()
    {
        $this->query_node = new QueryNode();
        $this->addMethod('offset')->addParameter('integer')->setAction(function(&$node, $offset)
        {
            $node->offset(new IntegerNode($offset)); 
        });
        $this->addMethod('offset')->addParameter('callback')->setAction(function(&$node, $offset)
        {
            $this->offset($offset());
        });
        $this->addMethod('where')
            ->addParameter('callback')
            ->addParameter('*')
            ->addParameter('*')
            ->setAction('recall');
        $this->addMethod('where')
            ->addParameter('string')
            ->addParameter('string')
            ->addParameter('string')
            ->setAction(function($node, $operand, $operator, $condition)
            {
                $new_node = new BinaryNode($operator);
                $new_node->left  = $this->parse($operand);
                $new_node->right = $this->parse($condition);
                
                if ($left = $node->getWhere()) {
                    $connecting_node = new BinaryNode('&&');
                    $connecting_node->left = $left;
                    $connecting_node->right = $new_node;
                    $node->setWhere($connecting_node);
                } else {
                    $node->setWhere($new_node);                    
                }
             });
    }
    
    /**
     * Mainly for internal debugging purposes. Returns the current query node
     * @return QueryNode
     */
    public function getQueryNode(): QueryNode
    {
        return $this->query_node;    
    }
    
    /**
     * Checks if this query is readonly. If yes it throws an exception
     * 
     * @param string $feature
     */
    protected function checkForReadonly(string $feature)
    {
        if (static::$read_only) {
            throw new QueryNotWriteableException("The feature '$feature' is not avaiable, because this query is read-only");
        }
    }
    
    public function addMethod(string $name): MethodSignature
    {
        $signature = new MethodSignature();
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
        throw new WrongActionException("The action is invalid");
    }
    
    /**
     * Catchall for method calls that checks if the method starts with where, whereNot, orWhere or orWhereNot
     * 
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        if (!isset($this->methods[$name])) {
            throw new \Exception("Method '$name' not found");
        }
        foreach ($this->methods[$name] as $signature) {
            if ($signature->matches($arguments)) {
                return $this->performAction($signature->getAction(), $arguments);
            }
        }
    }

    // Other statements
    
    /**
     * First checks the query (if it is valid), prepares it and then Calls the query executor and returns its result
     * 
     * @param string $finalizer. This could be any of:
     * - "first": Returns the id that matches the conditions
     * - "get": Returns all ids that macthes the conditions
     * @param array $params
     */
    protected function executeQuery(string $finalizer, array $params = [])
    {
    }
    
// ============================================ Finalizing methods =============================================================    
    /**
     * Returns the first record that matches the conditions
     */
    public function first()
    {
        $id = $this->firstID();
        
        return Properties::loadRecord($id);
    }

    /**
     * Returns the first entry that matches the conditions or throws an exception if none exists
     */
    public function firstOrFail()
    {
        $id = $this->firstIDOrFail();
        
        return Properties::loadRecord($id);
    }

    /**
     * Returns the first id that matches the conditions or throws an exception if none exists
     */
    public function firstIDOrFail()
    {
        $result = $this->firstID();
        if (empty($result)) {
            throw new UnexpectedResultCountException("At least one result is expected for firstOrFail(), none returned");
        }
        
        return $result;
    }

    /**
     * Returns the first id that matches the conditions
     */
    public function firstID()
    {
        return $this->executeQuery( 'first' );        
    }
    
    /**
     * Returns only the value of the given row(s)
     */
    public function value($column)
    {
    }

    /**
     * Returns that record with id given id
     */
    public function find($id)
    {
    }

    /**
     * Returns only the given fields in a collection of stdClass of all items that match the conditions
     */
    public function pluck(... $fields)
    {
    }

    /**
     * Collects $number of entries and passes them to callback
     */
    public function chunk(int $number, callable $callback)
    {
    }

    /**
     *
     */
    public function chunkByID(int $number, callable $callback)
    {
    }
    
    /**
     * Returns the count of records that matches the conditions
     */
    public function count(): int
    {
        
    }

    /**
     * Returns the highest value of the given field
     */
    public function max($field)
    {
    
    }

    /**
     * Returns the lowest value of the given field
     */
    public function min($field)
    {
    
    }

    /**
     * Calculates the average of the given field (when it is numeric)
     */
    public function avg(string $field): int|float
    {
    
    }

    /**
     * Sums up all values of the given field (when it is numeric)
     */
    public function sum($field): int|float
    {
    
    }

    /**
     * Returns true when at least one dataset matches the given condition
     */
    public function exists(): bool
    {
    
    }

    /**
     * Returns true when no dataset matches the given condition
     */
    public function doesntExist(): bool
    {
    
    }    
    
    /**
     * Returns all record that matches the conditions
     */
    public function get()
    {
        
    }
    
    /**
     * Returns a list of ids that matches the conditions
     */
    public function getIDs()
    {
        
    }
    
    /**
     * Expects excactly one result and returns it. If more or less it raises an exception
     */
    public function only()
    {
        
    }
    
    /**
     * Expects excactly one result and returns its id. If more or less it raises an exception
     */
    public function onlyID()
    {
        
    }
    
    /**
     * Deletes all records that macthes the conditions
     */
    public function delete()
    {
        $this->checkForReadonly('delete');
    }

    /**
     * Inserts a record into the set
     * @param unknown $data_set
     */
    public function insert($data_set)
    {
        $this->checkForReadonly('insert');
    }
    
    /**
     * Update all records that match the conditions
     * @param unknown $data_set
     */
    public function update($data_set)
    {
        $this->checkForReadonly('update');
    }
    
    /**
     * Inserts or updates a record depending of $condition
     * @param unknown $condition
     * @param unknown $data_set
     */
    public function upsert($condition, $data_set)
    {
        $this->checkForReadlnly('upsert');
    }
    
}
