<?php
/**
 * @file BasicQuery.php
 * A base class for other queries
 * Lang en
 * Reviewstatus: 2024-10-08
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Query/BasicQueryTest.php
 * Coverage: unknown
 */

namespace Sunhill\Query;

use Illuminate\Support\Collection;
use Sunhill\Query\Exceptions\InvalidOrderException;
use Sunhill\Query\Exceptions\NoResultException;
use Sunhill\Query\Exceptions\UnknownFieldException;
use Sunhill\Query\Exceptions\TooManyResultsException;
use Sunhill\Query\Exceptions\QueryNotWriteableException;

/**
 * The common ancestor for other queries. Defines the interface and some fundamental functions
 * for writing queries. Normally you will use one of the other basic query classes (like DatabaseQuery 
 * or ArrayQuery) 
 * 
 * @author klaus
 *
 */
abstract class BasicQuery
{

    /**
     * Defines if the query is writeable at all (if the findalizing methods delete(), update() and insert() 
     * work at all)
     * 
     * @var boolean
     */
    protected static $writeable = true;
    
    /**
     * When calling the offset() method, it writes the offset to this variable
     * 
     * @var integer
     */
    protected int $offset = 0;
    
    /**
     * When calling the limit() method, it writes the offset to this variable
     * 
     * @var integer
     */
    protected int $limit = 0;
    
    /**
     * When calling the order() method, it writes the order key to this variable
     * 
     * @var string
     */
    protected string $order_key = '';
    
    /**
     * When calling the order() method and pass a direction statement it is written to
     * this variable
     * 
     * @var string
     */
    protected string $order_direction = '';
    
    /**
     * The conditions are put in this array
     * 
     * @var array
     */
    protected array $conditions = [];
    
    public function __construct()
    {
    }

    /**
     * Assembles the query according to the given conditions and returns @author lokal
     * pseudo query that is further processed by a finalizing call.
     */
    abstract protected function assmebleQuery();
    
    /**
     * Returns the count of record that the previously assembled query returns
     * 
     * @param unknown $assambled_query
     * @return int
     */
    abstract protected function doGetCount($assambled_query): int;
    
    /**
     * Returns a Collection object of all records that match the given query conditions.
     * 
     * @param unknown $assembled_query
     */
    abstract protected function doGet($assembled_query): Collection;
    
    /**
     * Returns if the field exists or a pseudo field of that name exists
     * 
     * @param string $field
     * @return bool
     */
    abstract protected function fieldExists(string $field): bool;
    
    /**
     * Returns if the field can be uses as a sorting key
     * 
     * @param string $field
     * @return bool
     */
    abstract protected function fieldOrderable(string $field): bool;
    
    /**
     * Deletes the records that match the condition
     * Note: A check if the pool is writeable at all has already been performed. Read-only
     * queries can ignore this method
     * 
     * @param unknown $assembled_query
     * @return int
     */
    protected function doDelete($assembled_query): int
    {
        return 0;    
    }
    
    /**
     * Updates the records that match the condition
     * Note: A check if the pool is writeable at all has already been performed. Read-only
     * queries can ignore this method
     * 
     * @param unknown $assembled_query
     * @param array $fields
     * @return int
     */
    protected function doUpdate($assembled_query, array $fields): int
    {
        return 0;    
    }
    
    /**
     * Inserts one or more new records into the pool of records
     * Note: A check if the pool is writeable at all has already been performed. Read-only
     * queries can ignore this method
     *
     * @param unknown $assembled_query
     * @param array $fields
     * @return int
     */
    protected function doInsert($assembled_query, array $fields)
    {
        return null;
    }
    
    /**
     * This method does not have to necessarily be overwritten. By default it just return $record. 
     * In some cases it is necessary to return another type (e.g. Object).
     * 
     * @param unknown $record
     * @return unknown
     */
    protected function getRecord($key, $record)
    {
        return $record;
    }
    
    /** 
     * Fininalizing call that returns the number of records that match the 
     * given criteria
     * 
     * @return int
     */
    public function count(): int
    {
        return $this->doGetCount($this->assmebleQuery());
    }
    
    /**
     * Returns the first record that matches the given criteria. It raises an 
     * exception, if no element exists.
     * 
     * @param string|array of strings $fields either a single field or a list of
     * fields that should be returned be first. If none if given, all fields are
     * returned. 
     * 
     * @throws NoResultException::class
     */
    public function first($fields = null)
    {
        if (!is_null($result = $this->firstIfExists($fields))) {
            return $result;
        }
        throw new NoResultException("The query has no results and first() was called.");
    }
    
    /**
     * Returns the first record that mathes the given criteria or null if no record
     * exists. 
     * 
     * @param unknown $fields
     */
    public function firstIfExists($fields = null)
    {
         $result = $this->get($fields);
         
         if (empty($result)) {
             return null;
         } else {
            return $result->first();
         }
    }
    
    /**
     * Returns only a Collection of this single field
     * 
     * @param Collection $result
     * @param string $field
     * @return Collection
     */
    protected function mapField(Collection $result, string $field): Collection
    {
        return $result->map(function($item, $key) use($field) {
            return $item->$field;
        });
    }
    
    /**
     * Returns only a Collection of StdClasses of thie given fields
     * 
     * @param Collection $result
     * @param array $fields
     * @return Collection
     */
    protected function mapFields(Collection $result, array $fields): Collection
    {
        return $result->map(function($item, $key) use($fields) {
            $result = new \StdClass();
            foreach($fields as $field) {
                $result->$field = $item->$field;
            }
            return $result;
        });            
    }
    
    /**
     * Returns all records that matches the given criteria
     * 
     * @param unknown $fields
     * @return Collection
     */
    public function get($fields = null): Collection
    {
        $result = $this->doGet($this->assmebleQuery(),$fields);
        
        if (is_string($fields)) {
            return $this->mapField($result,$fields);
        } else if (is_array($fields)) {
            return $this->mapFields($result,$fields);
        } else {
            return $result->map(function($item, $key) {
                return $this->getRecord($key, $item);
            });
        }
        return $result;
    }

    /**
     * Expect excactly one result and returns is or throws an exception.
     * 
     * @param unknown $fields
     */
    public function only($fields = null)
    {
        $query = $this->assembleQuery();
        $count = $query->count();
        if ($count < 1) {
            throw new NoResultException("Excactly one result expected, none was returned.");
        }
        if ($count > 1) {
            throw new TooManyResultsException("Excactly one result expected, '$count' where returned.");
        }
        return $query->first();
    }
    
    /**
     * The records matching the given condition are deleted (if query is writeable at all)
     */
    public function delete()
    {
        $this->checkWriteable();
    }
    
    /**
     * The record matching the given condition are updated with the given parameters
     * 
     * @param array $fields
     */
    public function update(array $fields)
    {
        $this->checkWriteable();        
    }
    
    /**
     * A new record is inserted into the pool.
     * 
     * @param array $fields
     */
    public function insert(array $fields)
    {
        $this->checkWriteable();        
    }
    
    public function offset(int $offset): BasicQuery
    {
        $this->offset = $offset;
        return $this;
    }
    
    public function limit(int $limit): BasicQuery
    {
        $this->limit = $limit;
        return $this;
    }
    
    public function where($key, $relation = null, $value = null): BasicQuery
    {
        $this->addCondition('and', $key, $relation, $value);
        return $this;        
    }
    
    public function orWhere($key, $relation = null, $value = null): BasicQuery
    {
        $this->addCondition('or', $key, $relation, $value);
        return $this;
    }
    
    public function whereNot($key, $relation = null, $value = null): BasicQuery
    {
        $this->addCondition('andnot', $key, $relation, $value);
        return $this;        
    }
    
    public function orWhereNot($key, $relation = null, $value = null): BasicQuery
    {
        $this->addCondition('ornot', $key, $relation, $value);
        return $this;
    }
    
    protected function handleNestedCondition(callable $key)
    {
        $nested = new static();
        $key($nested);
        return $nested;
    }
    
    protected function checkAndAdjustCondition($key, $relation ,$value)
    {
        if (is_null($value)) {
            $value = $relation;
            $relation = '=';
        }
        return [$key, $relation, $value];
    }
    
    protected function addCondition(string $connection, $key, $relation, $value)
    {
        $entry = new \StdClass();
        $entry->connection  = $connection;
        
        if (is_callable($key)) {
            $entry->key = $this->handleNestedCondition($key);
        } else {
            list($key,$relation,$value) = $this->checkAndAdjustCondition($key, $relation, $value);
            
            $entry->key         = $key;
            $entry->relation    = $relation;
            $entry->value       = $value;            
        }
        $this->conditions[] = $entry;
    }
    
    public function getConditions(): array
    {
        return $this->conditions;    
    }
    
    public function orderBy($key, $direction = 'asc'): BasicQuery
    {
        $direction = strtolower($direction);
        if (!in_array($direction,['asc','desc'])) {
            throw new InvalidOrderException("'$direction' is not a valid order direction.");
        }
        if (!$this->fieldExists($key)) {
            throw new UnknownFieldException("'$key' is not a valid field or pseudo field.");
        }
        if (!$this->fieldOrderable($key)) {
            throw new InvalidOrderException("'$key' is usable as a sorting key.");
        }
        $this->order_key = $key;
        $this->order_direction = $direction;
        return $this;
    }
    
    protected function checkWriteable()
    {
        if (!static::$writeable) {
            throw new QueryNotWriteableException("The query is not writeable.");
        }
    }
}