<?php
/**
 * @file BasicQuery.php
 * A base class for other queries
 * Lang en
 * Reviewstatus: 2024-10-08
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
use Sunhill\Basic\Base;
use Sunhill\Query\Exceptions\InvalidStatementException;
use phpDocumentor\Reflection\Types\Static_;
use phpDocumentor\Reflection\Types\Mixed_;
use Illuminate\Support\Str;

/**
 * The common ancestor for other queries. Defines the interface and some fundamental functions
 * for writing queries. Normally you will use one of the other basic query classes (like DatabaseQuery 
 * or ArrayQuery) 
 * 
 * @author klaus
 *
 */
abstract class BasicQuery extends Base
{
    
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
    
    protected $where_statements = [];
    
    protected $order_fields = [];
    
    protected $group_fields = [];
    
    protected $limit = 0; 
    
    protected $offset = 0; 
    
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
            throw new InvalidStatementException("A order statement was used with feature '$feature'");
        }
        if ($this->offset) {
            throw new InvalidStatementException("A order statement was used with feature '$feature'");
        }
    }
    
    // Where statements
    /**
     * Parses the argument list of of function 
     * 
     * @param unknown $argument
     * @return unknown[]|\stdClass[]
     */
    private function getArgumentList($argument)
    {
        $result = [];
        foreach (explode(',',$argument) as $single_argument) {
            $result[] = $this->getField($single_argument);
        }
        return $result;
    }
       
    protected function hasProperty(string $test): bool
    {
        
    }
    
    protected function getField(string $field)
    {
        $result = new \stdClass();
        if (preg_match('/^([a-zA-Z_][_[:alnum:]]*)\((.*)\)$/',$field,$matches)) {
            $result->type = 'function';
            $result->function = $matches[1];
            $result->argument = $this->getArgumentList($matches[2]);
            return $result;
        } 
        if (preg_match('/([a-zA-Z_][_[:alnum:]]*)->(.*)/',$field,$matches)) {
            $result->type = 'reference';
            $result->parent = $matches[1];
            $result->reference = $this->getField($matches[2]);
            return $result;
        }
        if (preg_match('/\"(.*)\"/',$field,$matches)) {
            return makeStdClass(['type'=>'const','value'=>$matches[1]]);
        }
        if (preg_match("/\'(.*)\'/",$field,$matches)) {
            return makeStdClass(['type'=>'const','value'=>$matches[1]]);
        }
        if ($this->hasProperty($field)) {
            // if it consist only of allowed characters for a field we assume a field. We have to decide later
            return makeStdClass(['type'=>'field','name'=>$field]);
        }
        return makeStdClass(['type'=>'const','value'=>$field]);
    }

    private function parseFieldOrCondition($test)
    {
        if (is_string($test)) {
            return $this->getField($test);
        }
        if (is_scalar($test)) {
            return makeStdClass(['type'=>'const','value'=>$test]);
        }
        if (is_a($test, BasicQuery::class)) {
            return makeStdClass(['type'=>'subquery','value'=>$test]);
        }
        if (is_array($test) || is_a($test, \Traversable::class)) {
            return makeStdClass(['type'=>'array', 'value'=>$test]);
        }
        if (is_callable($test)) {
            return makeStdClass(['type'=>'callback','value'=>$test]);
        }
    }
    
    protected function addWhereStatement(string $connect, string $field, string $operator, $condition)
    {
        $entry = new \stdClass();
        $entry->connect = $connect;
        $entry->field = $this->parseFieldOrCondition($field);
        $entry->operator = $operator;
        $entry->condition = $this->parseFieldOrCondition($condition);
        $this->where_statements[] = $entry;
    }
    
    private function checkMethodStartsWith(string $name, string $start, string $connection, array $arguments)
    {
        if (Str::startsWith($name,$start)) {
            if ($name == $start) {
                $this->addWhereStatement($connection, $arguments[0],$arguments[1]??null,$arguments[2]??null);
            } else {
                $this->addWhereStatement($connection, $arguments[0],strtolower(Str::substr($name,strlen($start))),$arguments[1]??null);
            }
            return true;
        }
        return false;
    }
    
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
    
    public function order(string $field, string $direction = 'asc'): static
    {
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
        $this->limit = $limit;
        return $this;
    }
    
    /**
     * Indicates that only the entries beginning with $offset should be returned
     * @param int $offset
     * @return static
     */
    public function offset(int $offset): static
    {
        $this->offset = $offset;
        return $this;
    }
    
    // Finalizing methods
    
    abstract protected function doAssembleQuery();
    
    private function checkWhereConditions()
    {
        
    }
    
    private function checkOrderConditions()
    {
        
    }
    
    private function checkGroupConditions()
    {
        
    }

    /**
     * This method does some prechecks, if the statement are valid at all
     */
    protected function precheckQuery()
    {
        if (!empty($this->where_statements)) {
            $this->checkWhereConditions();
        }
        if (!empty($this->order_fields)) {
            $this->checkOrderConditions();
        }
        if (!empty($this->group_fields)) {
            $this->checkGroupConditions();
        }        
    }
    
    /**
     * Assembles the parsed query to something the finalizing methods can use to execute the query
     */
    protected function assembleQuery()
    {
        $this->precheckQuery();
        $this->doAssembleQuery();
    }
    
    /**
     * Returns the first record that matches the conditions
     */
    public function first()
    {
        $this->assembleQuery();
    }
    
    /**
     * Returns the count of records that matches the conditions
     */
    public function count(): int
    {
        
    }
    
    /**
     * Returns all record that matches the conditions
     */
    public function get()
    {
        
    }
    
    /**
     * Returns the first id that matches the conditions
     */
    public function firstID()
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