<?php

namespace Sunhill\Query;

use Illuminate\Support\Collection;
use Sunhill\Basic\Query\Exceptions\InvalidOrderException;
use Sunhill\Basic\Query\Exceptions\NoUnaryConditionException;
use Doctrine\DBAL\Driver\Middleware\AbstractConnectionMiddleware;
use Sunhill\Query\Exceptions\NoResultException;

abstract class BasicQuery
{

    
    public function __construct()
    {
    }

    /**
     * Assembles the query according to the given conditions and returns @author lokal
     * pseudo query that is further processed by a finalizing call.
     */
    abstract protected function assmebleQuery();
    
    abstract protected function doGetCount($assambled_query): int;
    
    /** 
     * Fininalizing call that returns the number of records that match the 
     * given criteria
     * 
     * @return int
     */
    public function count(): int
    {
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
        if ($result = $this->firstIfExists($fields)) {
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
        
    }
    
    public function get($fields = null): Collection
    {
        $this->targetGet();
        return $this->execute();
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
        $this->condition_builder->where($key, $relation, $value);
        return $this;        
    }
    
    public function orWhere($key, $relation = null, $value = null): BasicQuery
    {
        $this->condition_builder->orWhere($key, $relation, $value);
        return $this;
    }
    
    public function whereNot($key, $relation = null, $value = null): BasicQuery
    {
        $this->condition_builder->whereNot($key, $relation, $value);
        return $this;        
    }
    
    public function orWhereNot($key, $relation = null, $value = null): BasicQuery
    {
        $this->condition_builder->orWhereNot($key, $relation, $value);
        return $this;
    }
    
    public function orderBy($key, $direction = 'asc'): BasicQuery
    {
        $direction = strtolower($direction);
        if (!in_array($direction,['asc','desc'])) {
            throw new InvalidOrderException("'$direction' is not a valid order direction.");
        }
        $this->order_key = $key;
        $this->order_direction = $direction;
        return $this;
    }
    
    abstract protected function execute();    
    
    protected function unaryCondition($key)
    {
        throw new NoUnaryConditionException("This query doesn't define a unary condition");    
    }
        
    protected function binaryCondition($combine, $key, $relation, $value)
    {
        $entry = new \StdClass();
        $entry->combine = $combine;
        $entry->key = $key;
        $entry->relation = $relation;
        $entry->value = $value;
        $this->conditions[] = $entry;
    }
    
    protected function arrayToCollection(array $input): Collection
    {
        return collect($input);
    }
}