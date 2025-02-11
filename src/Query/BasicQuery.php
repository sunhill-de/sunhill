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
    
    /**
     * If not empty this list lists the fields that should be returned by first(), only() or get()
     * @var array
     */
    protected $fields = [];
    
    /**
     * Here the where statement are stored (empty if there is no where condition)
     * @var array
     */
    protected $where_statements = [];
    
    /**
     * A list of field that indicate the ordering of the result
     * @var array
     */
    protected $order_fields = [];

    /**
     * A list of fields to which the result should be grouped
     * @var array
     */
    protected $group_fields = [];
    
    /**
     * Indicates the limit (the maximum number of results of get() and getIDs()
     * @var integer
     */
    protected $limit = 0; 
    
    /**
     * Indicates the first result that should be retured of the result set of get() and getIDs()
     * @var integer
     */
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
            throw new InvalidStatementException("A limit statement was used with feature '$feature'");
        }
        if ($this->offset) {
            throw new InvalidStatementException("A offset statement was used with feature '$feature'");
        }
    }
    
    // Where statements
    
    /**
     * Helper function that parses the argument list of of function 
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
    
    /**
     * Returns true if this record has the given property "$etst"
     * 
     * @param string $test
     * @return bool
     */
    protected function hasProperty(string $test): bool
    {
        
    }
    
    /**
     * Try to parse a string to a field
     * 
     * @param string $field
     * @return \stdClass|StdClass
     */
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

    /**
     * Try to parse the given parameter to something the query can process
     * 
     * @param unknown $test
     * @return stdClass|\Sunhill\Query\StdClass|StdClass
     */
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
        throw new InvalidStatementException("The given parameter was not parsable for a query");
    }
    
    /**
     * Adds a where statement to the query
     * 
     * @param string $connect
     * @param string $field
     * @param string $operator
     * @param unknown $condition
     */
    protected function addWhereStatement(string $connect, string $field, string $operator, $condition)
    {
        $entry = new \stdClass();
        $entry->connect = $connect;
        $entry->field = $this->parseFieldOrCondition($field);
        $entry->operator = $operator;
        $entry->condition = $this->parseFieldOrCondition($condition);
        $this->where_statements[] = $entry;
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
                $this->addWhereStatement($connection, $arguments[0],$arguments[1]??null,$arguments[2]??null);
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