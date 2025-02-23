<?php
/**
 * @file BasicQuery.php
 * A base class for other queries
 * Lang en
 * Reviewstatus: 2025-02-18
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Query/BasicQueryTest.php
 * Coverage: 77.782% (2024-10-17)
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

/**
 * The common ancestor for other queries. Defines the interface and some fundamental functions
 * for writing queries. Normally you will use one of the other basic query classes (like DatabaseQuery 
 * or ArrayQuery) 
 * 
 * @author klaus
 *
 */
abstract class BasicQuery extends QueryHandler
{

    /**
     * The constructor creates a new QueryObject
     */
    public function __construct()
    {
        $this->setQueryObject(new QueryObject());
    }
    
    /**
     * A static boolean that indicates if this query is readonly
     * @var boolean
     */
    protected static $read_only = false;
    
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
    
    /**
     * For some operations (insert and upserts) a where, limit, offset, order or group statement 
     * makes no sense. So if one of those are set, throw an exception.
     * 
     * @param string $feature
     */
    protected function checkForNoConditions(string $feature)
    {
        if (!empty($this->where_statements)) {
            throw new InvalidStatementException("A where condition was used with feature '$feature'");            
        }
        if (!empty($this->order_fields)) {
            throw new InvalidStatementException("A order statement was used with feature '$feature'");
        }
        if (!empty($this->group_fields)) {
            throw new InvalidStatementException("A group statement was used with feature '$feature'");
        }
        if ($this->limit) {
            throw new InvalidStatementException("A limit statement was used with feature '$feature'");
        }
        if ($this->offset) {
            throw new InvalidStatementException("A offset statement was used with feature '$feature'");
        }
    }
    
    // Where statements
    
    /**
     * Adds a where statement to the query
     * 
     * @param string $connect
     * @param string $field
     * @param string $operator
     * @param unknown $condition
     */
    protected function addWhereStatement(string $connect, $field, $operator, $condition)
    {
        $tokenizer = new Tokenizer($this->structure);
        $this->getQueryObject()->addWhereStatement(
            $connect,
            $tokenizer->parseParameter($field, ['field','function_of_field','callback','subquery']),
            $operator,
            $tokenizer->parseParameter($condition, ['field','const','callback','array_of_constants','subquery','function_of_field','function_of_value'])
        );
    }

    /**
     * When a where statement is passed a single parameter this can only be a callable. This indicates that this is a bracket where statement
     */
    private function addBracketStatement(string $connect, $argument)
    {
        if (!is_callable($argument)) {
             throw new InvalidStatementException("A where statement needs at least 2 parameter");
        }
        $this->addWhereStatement($connect, "", "()", $argument);
    }
    
    private function addDefaultWhereStatement(string $connection, array $arguments)
    {
                switch (count($arguments)) {
                    case 0:
                    case 1:
                      $this->addBracketStatement($connection, $arguments[0]);
                      break;
                    case 2:  
                      $this->addWhereStatement($connection, $arguments[0],"=",$arguments[1]);
                      break;
                    case 3:  
                      $this->addWhereStatement($connection, $arguments[0],$arguments[1],$arguments[2]);
                      break;
                    default:                      
                }    
    }
    
    /**
     * Helper function that checks if a method named "$name" starts with "$start". If yes it checks if $name 
     * is excactly $start. If yes add the condition with the given connection and other arguments. If not
     * take the rest of the method name, make it an condition and add the where statement
     * 
     * @param string $name
     * @param string $start
     * @param string $connection
     * @param array $arguments
     * @return boolean
     */
    private function checkMethodStartsWith(string $name, string $start, string $connection, array $arguments)
    {
        if (Str::startsWith($name,$start)) {
            if ($name == $start) {
                $this->addDefaultWhereStatement($connection,$arguments); 
            } else {
                $this->addWhereStatement($connection, $arguments[0],strtolower(Str::substr($name,strlen($start))),$arguments[1]??null);
            }
            return true;
        }
        return false;
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
        if ($this->checkMethodStartsWith($name, "whereNot", "andnot", $arguments)) {
            return $this;
        }
        if ($this->checkMethodStartsWith($name, "orWhereNot", "ornot", $arguments)) {
            return $this;
        }
        if ($this->checkMethodStartsWith($name, "where", "and", $arguments)) {
            return $this;
        }
        if ($this->checkMethodStartsWith($name, "orWhere", "or", $arguments)) {
            return $this;
        }
        throw new \Exception("Method '$name' not found");
    }

    // Other statements
    
    /**
     * A statement that indicates that only of subsets of the records should be returned 
     * 
     * @param unknown $fields
     * @return \Sunhill\Query\BasicQuery
     */
    public function fields($fields)
    {
        if (is_array($fields) || is_a($fields, \Traversable::class)) {
            foreach ($fields as $single_field) {
                $this->fields[] = $this->parseFieldOrCondition($single_field);                
            }
        } else if (is_string($fields)) {
            foreach (explode(",",$fields) as $single_field) {
                    $this->fields[] = $this->parseFieldOrCondition($single_field);
            }
        } else {
                $this->fields[] = $this->parseFieldOrCondition($fields);
        }
        return $this;
    }
    
    public function order($field, string $direction = 'asc'): static
    {
        $direction = strtolower($direction);
        if (($direction !== "asc") && ($direction !== 'desc')) {
            throw new InvalidOrderException("The direction $direction is invalid");
        }
        if (is_string($field)) {
            if (!$this->hasProperty($field)) {
                throw new InvalidOrderException("It's not possible to order by '$field'");                    
            }
            $this->order_fields[] = makeStdclass(['type'=>'field','field'=>$field,'direction'=>$direction]);
        } else if (is_callable($field)) {
            $this->order_fields[] = makeStdclass(['type'=>'callback','callback'=>$field,'direction'=>$direction]);            
        } else {
            throw new InvalidOrderException(getScalarMessage("It's not possible to sort by given variable :variable", $field));
        }
        return $this;    
    }
    
    /**
     * Indicates that only $limit entries of the result set should be returned
     * 
     * @param int $limit
     * @return static
     */
    public function limit(int $limit): static
    {
        $this->getQueryObject()->setLimit($limit);

        return $this;
    }
    
    /**
     * Indicates that only the entries beginning with $offset should be returned
     * @param int $offset
     * @return static
     */
    public function offset(int $offset): static
    {
        $this->getQueryObject()->setOffset($offset);

        return $this;
    }
    
    // Finalizing methods
    
    protected function getQueryExecutor()
    {
        
    }
    
    protected function doExecuteQuery()
    {
        
    }
    
    /**
     * Returns a query checker. This method can be overwritten for implementing extended checkers
     * 
     * @return \Sunhill\Query\Checker
     */
    protected function getQueryChecker()
    {
        return new Checker();
    }
    
    protected function checkQuery(string $finalizer)
    {
        $checker = $this->getQueryChecker( $this->getQueryObject() );
        $checker->check();        
    }
    
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
        $this->checkQuery($finalizer);
        return $this->doExecuteQuery( $finalizer, $params );
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
    public function usert($condition, $data_set)
    {
        $this->checkForReadlnly('upsert');
    }
    
}
