<?php
/**
 * @file QueryObject.php
 * A class that stores information about a query to pass between builder, checker and executor
 * Lang en
 * Reviewstatus: 2025-02-18
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Query/QueryObjectTest.php
 * Coverage: 
 */

namespace Sunhill\Query;

use Sunhill\Basic\Base;
use Sunhill\Query\Exceptions\StructureMissingException;

class QueryObject extends Base
{
    /**
     * Stores the structure of the owning PooledRecord
     * 
     * @var unknown
     */
    protected $structure;
    
    /**
     * Setter for $structure
     * 
     * @param \stdClass $structure
     * @return \Sunhill\Query\QueryObject
     */
    public function setStructure(\stdClass $structure)
    {
        $this->structure = $structure;
        return $this;
    }

    /**
     * Checks if a structure is set. If not, throws an excpetion
     */
    private function checkForStructure()
    {
        if (is_null($this->structure)) {
            throw new StructureMissingException("A structure was expected but none is set");         
        }    
    }

    /**
     * Returns the current set structure or throws an exception if none is set
     */
    public function getStructure(): \stdClass
    {
        $this->checkForStructure();    
        return $this->structure;
    }

    /**
     * Returns true if the structure defines the given field
     * @param $field_name string the name of the field to test
     * @return bool true if the field exists, otherwise false
     */
    public function hasField(string $field_name): bool
    {
        return isset($this->getStructure()->elements[$field_name]);
    }

    /**
     * Return the type of the given field
     * @param $field_name the name of the field to return the type of
     * @return string the type of the field
     */
    public function getFieldType(string $field_name): string
    {
        return $this->getStructure()->elements[$field_name]->type;
    }
    
    /**
     * If not empty this list lists the fields that should be returned by first(), only() or get()
     * @var array
     */
    protected $fields = [];

    /**
     * Adds one or more fields that should be returned
     */
    public function addFields($fields): static
    {
        if (is_array($fields) || is_a($fields, \Traversable::class)) {
            foreach ($fields as $field) {
                $this->fields[] = $field;
            }    
        }  else  {
           $this->fields[] = $fields;
        }
        
        return $this;
    }

    /**
     * Returns the current set fields 
     */
    public function getFields(): array
    {
        return $this->fields;
    }
        
    /**
     * A list of field that indicate the ordering of the result
     * @var array
     */
    protected $order_fields = [];

    /**
     * Adds an additional order statement
     * @field the field to sort
     * @direction the direction to sort
     */
    public function addOrder($field, string $direction)
    {
        $this->order_fields[] = makeStdClass(['field'=>$field, 'dir'=>$direction]);
    }

    /**
     * Returns all currently set order statements
     * @return array the current order statements
     */
    public function getOrderStatements(): array
    {
        return $this->order_fields;
    }
    
    /**
     * A list of fields to which the result should be grouped
     * @var array
     */
    protected $group_fields = [];

    public function addGroupField($group)
    {
        if (is_array($group) || is_a($group, \Traversable::class)) {
            foreach ($group as $single_group) {
                $this->group_fields[] = $single_group;
            }    
        } else {
            $this->group_fields[] = $group;
        }    
    }

    public function getGroupFields(): array
    {
        return $this->group_fields;
    }
    
    /**
     * Indicates the limit (the maximum number of results of get() and getIDs()
     * @var integer
     */
    protected $limit = 0;
    
    /**
     * Setter for $offset
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }

    /**
     * Getter for $offset
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
    
    /**
     * Indicates the first result that should be retured of the result set of get() and getIDs()
     * @var integer
     */
    protected $offset = 0;

    /**
     * Setter for $offset
     */
    public function setOffset(int $offset)
    {
        $this->offset = $offset;
    }

    /**
     * Getter for $offset
     */
    public function getOffset(): int
    {
        return $this->offset;
    }
    
    /**
     * Here the where statement are stored (empty if there is no where condition)
     * @var array
     */
    protected $where_statements = [];

    /**
     * Adds a new where statement to the where statements
     */
    public function addWhereStatement(string $connection, $field, $operator, $relation)
    {           
        $entry = new \stdClass();
        $entry->connect = $connection;        
        $entry->field = $field;
        $entry->operator = $operator;
        $entry->condition = $relation;
        
        $this->where_statements[] = $entry;
    }

    /**
     * Returns the collected where statements
     */
    public function getWhereStatements(): array
    {
        return $this->where_statements;
    }    
    
}
