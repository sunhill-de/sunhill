<?php
/**
 * @file QueryNode.php
 * A node that represents all informations about a query. That includes fields, wheres, groups, havings, 
 * orders, offset and limit
 * Note: This class should not be initiated manually but instead be created by the Query class
 * 
 * Lang en
 * Reviewstatus: 2025-07-09
 * Creation date: 2025-04-25
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Query/QueryParser/Nodes/QueryNodeTest.php
 * Coverage Unit:
 */

namespace Sunhill\Query\QueryParser\Nodes;

use Sunhill\Parser\Nodes\Node;
use Sunhill\Query\Exceptions\InvalidStatementException;
use Sunhill\Parser\Nodes\ArrayNode;

/**
 * The QueryNode is a collector for all informations that is needed to execute a query
 * 
 * @author klaus
 *
 */
class QueryNode extends Node
{
    
    /**
     * This variable stores all storage-ids that are needed by this query. Every storage-id is assigned
     * an alias (like b.storageid that makes it easier for the executor to group the access
     * 
     * @var array
     */
    protected array $storages = [];
    
    /**
     * Internal variable that just stores what letter is to be used as the next alias
     * 
     * @var string
     */
    private $next_storage_alias = 'b';
    
    /**
     * Creates an empty QueryNode
     */
    public function __construct()
    {
        parent::__construct('query',[
            'fields'=>null,
            'verb'=>'select',
            'offset'=>null,
            'limit'=>null,
            'order'=>null,
            'group'=>null,
            'having'=>null,
            'where_condition'=>null            
        ]);   
    }
    
    /**
     * Getter or setter for verb. The verb is the kind of action that should be performed
     * on the query. The verb could be one on 'selecT', 'delete', 'update', 'insert', 'first', 'count'
     * 'first' and 'count' can be mapped to other queries by the executor
     * @param string $verb
     * @return \Sunhill\Query\QueryParser\Nodes\QueryNode|NULL|mixed
     */
    public function verb(?string $verb = null)
    {
        return $this->handleReplacingChild('verb', $verb);
    }

    /**
     * When omitted, the query returns only the id. With with method it is possible to return only
     * certain fields of a record and not the whole record. This method can be called repeatedly to
     * add aditional fields
     * 
     * @param Node $fields
     * @return \Sunhill\Query\QueryParser\Nodes\QueryNode|NULL|mixed
     */
    public function fields(?Node $fields = null)
    {
        return $this->handleOptionalArrayChild('fields', $fields);    
    }
    
    /**
     * When omitted the query is executed beginning from the first record that matches the conditions.
     * When an positive integer is given the query executed beginning with the $offset-th record
     * 
     * @param int $node
     * @return int|NULL
     */
    public function offset(?int $offset = null)
    {
        return $this->handleReplacingChild('offset', $offset);
    }
    
    /**
     * When omitted the query is executed for all records that matches the condition. If an positive  integer is
     * than onliy the count of $limit of records are processed.
     * 
     * @param Node $limit
     * @return \Sunhill\Query\QueryParser\Nodes\QueryNode|NULL|mixed
     */
    public function limit(?int $limit = null)
    {
        return $this->handleReplacingChild('limit', $limit);
    }
    
    /**
     * When omitted the query results are not sorted at all, otherwise a sorting field could be passed with this 
     * method. It's possible to call it repeatedly to define a secondary key if the first are the same.
     * 
     * @param Node $node
     * @return \Sunhill\Query\QueryParser\Nodes\QueryNode|NULL|mixed
     */
    public function order(?Node $node = null)
    {
        return $this->handleOptionalArrayChild('order', $node);
    }
    
    /**
     * When omitted the query results are not grouped otherwise this method adds a field that should be grouped
     * be. This makes sense for aggregate functions like sum(), min(), max() and avg()
     * 
     * @param Node $node
     * @return \Sunhill\Query\QueryParser\Nodes\QueryNode|NULL|mixed
     */
    public function group(?Node $node = null)
    {
        return $this->handleOptionalArrayChild('group', $node);
    }
    
    /**
     * When omitted no filter coniditions are applied to the query otherwise the search results are filterd#
     * according to this conditions. This method should olny be called once because it replaces any where
     * statement that was applied earlier.
     * 
     * @param Node $node
     * @return Node|NULL
     */
    public function where(?Node $node = null): ?Node
    {
        return $this->handleReplacingChild('where_conditions', $node);
    }
    
    /**
     * When omitted no post grouping filter conditions are applied otherwise it's possible to filter the
     * search results again after grouping.
     * 
     * @param Node $node
     * @return Node|NULL
     */
    public function having(?Node $node = null): ?Node
    {
        return $this->handleReplacingChild('having', $node);
    }
    
    /**
     * Additional getter for the where statement.
     * 
     * @todo Check if obsolete
     * @return Node|NULL
     */
    public function getWhere(): ?Node
    {
        return isset($this->children['where_conditions'])?$this->children['where_conditions']:null;        
    }

    /**
     * Searches if there is already one reference to this storageid that matches the same condition-
     * When found return its alias otherwise return null.
     * 
     * @param string $storage_name
     * @param string $type
     * @param string $target_alias
     * @param string $field
     * @return string|NULL
     */
    private function searchAlias(string $storage_name, string $type, string $target, string $field, string $target_field): ?string
    {
        if (($storage_name == $this->storages['a']->storage) && ($type == 'inner')) { // Trivial, just refers to the main table
            return 'a';
        }
        foreach ($this->storages as $alias => $storage) {
            if (($storage->storage == $storage_name) && 
                (($storage->join == $type) || (($storage->join == 'first') && ($type == 'inner')))) {
                    return $alias;
                }
        }
        return null;
    }
    
    /**
     * Adds a storageid to the storages table
     * 
     * @param string $storage_name
     * @param string $type
     * @param string $target_alias
     * @param string $field
     * @return string
     */
    private function addAlias(string $storage_name, string $type, ?string $target, string $field, string $target_field): string
    {
        $alias = $this->next_storage_alias++;
        $this->storages[$alias] = new \stdClass();
        $this->storages[$alias]->storage = $storage_name;
        $this->storages[$alias]->join = $type;
        $this->storages[$alias]->target = $target;
        $this->storages[$alias]->field = $field;
        $this->storages[$alias]->target_field = $target_field;
        
        return $alias;
    }

    private function addFirstAlias(string $storage_name)
    {
        $this->storages['a'] = new \stdClass();
        $this->storages['a']->storage = $storage_name;
        $this->storages['a']->join = 'first';
        $this->storages['a']->target = null;
        $this->storages['a']->field = null;
        $this->storages['a']->target_field = null;
        
        return 'a';
    }
    
    /**
     * Whenever the analyzer detect a reference to a storage it adds the storage to the storage table of
     * the query node. The execute can use this stable to build the resulting query (building a join statement, etc)
     * 
     * @param string $storage_name
     * @param string $type
     * @param string $target_alias
     * @param string $field
     * @return string
     */
    public function addStorage(string $storage_name, string $type = 'inner', ?string $target = null, string $field = 'id', string $target_field = 'id'): string
    {
        if(empty($this->storages)) {
            return $this->addFirstAlias($storage_name);
        }
        if (is_null($target)) {
            $target = $this->storages['a']->storage; // Default refer to main table
        }
        if ($alias = $this->searchAlias($storage_name, $type, $target, $field, $target_field)) {
            return $alias;
        }
        return $this->addAlias($storage_name, $type, $target, $field, $target_field);
    }
    
    /**
     * Returns all storages that are used
     * 
     * @return array
     */
    public function getStorages(): array
    {
        return $this->storages;
    }

    private function verbToString(): string
    {
        switch ($this->verb()) {
            case 'get':
            case 'first':
            case 'select':    
                return 'SELECT';
                break;
            case 'delete':
                return 'DELETE';
            case 'update':
                return 'UPDATE';
            case 'insert':
                return 'INSERT';                
        }
        throw new \Exception("Unknown verb: ".$this->verb());
    }
    
    private function fieldsToString(): string
    {
        if (!$this->fields()) {
            return ' *';
        }
        if (is_a($this->fields(),ArrayNode::class)) {
            $result = '';
            $first = true;
            for ($i=0;$i<$this->fields()->elementCount();$i++) {
                $result .= ($first?"":", ").$this->fields()->getElement($i)->toString();
                $first = false;
            }
            return $result;
        }
        $ths->fields()->toString();
    }
    
    private function storagesToString(): string
    {
        $result = ' FROM ';
        $first = true;
        foreach ($this->storages as $alias => $storage)
        {
            if (!$first) {
                switch ($storage->join) {
                    case 'inner':
                        $result .= 'INNER JOIN ';
                        break;
                    case 'left':
                        $result .= 'LEFT OUTER JOIN ';
                        break;
                    case 'right':
                        $result .= 'RIGHT OUTER JOIN ';
                        break;
                }
            }
            $result .= $storage->storage;
            $result .= ' AS '.$alias;
            if (!$first) {
                $result .= ' ON ';
            }
        }
        return $result;
    }
    
    /**
     * Converts the node to a string
     * 
     * {@inheritDoc}
     * @see \Sunhill\Parser\Nodes\Node::toString()
     */
    public function toString(): string
    {
        return $this->verbToString().$this->fieldsToString().$this->storagesToString();
    }

    /**
     * Helper function that checks if the given node is not null. If it os not null call validate()
     * 
     * @param Node $node
     */
    private function validateOptionalNull(?Node $node)
    {
        if ($node) {
            $node->validate();
        }
    }
    
    /**
     * Validates if verb is correctly
     */
    private function validateVerb()
    {
        if (!in_array(strtolower($this->verb()),['select','first','count','update','delete','insert'])) {
            throw new InvalidStatementException("The verb '".$this->verb()."' is not valid");
        }        
    }
    
    /**
     * Validate if fields are correctly
     */
    private function validateFields()
    {
        $this->validateOptionalNull($this->fields());        
    }
    
    /**
     * Validate if order is correctly
     */
    private function validateOrder()
    {
        $this->validateOptionalNull($this->order());        
    }
    
    /**
     * Validate if offset is correctly
     */
    private function validateOffset()
    {
        if (!is_null($this->offset()) && ($this->offset() < 0)) {
            throw new InvalidStatementException("Offset must be a positive integer (or 0)");
        }        
    }
    
    /**
     * Validate if limit is correctly
     */
    private function validateLimit()
    {
        if (!is_null($this->limit()) && ($this->limit() < 1)) {
            throw new InvalidStatementException("Limit must be a positiv numer greater than 0");
        }        
    }
    
    /**
     * Validate if where is correctly
     */
    private function validateWhere()
    {
        $this->validateOptionalNull($this->where());
    }
    
    /**
     * Validate if having is correctly
     */
    private function validateHaving()
    {
        $this->validateOptionalNull($this->having());        
    }
    
    /**
     * Validate if groupBy is correctly
     */    
    private function validateGroupBy()
    {
        $this->validateOptionalNull($this->group());        
    }
    
    /**
     * Validates if the QueryNode is correctly by checking if each of the subnodes are correctly
     * {@inheritDoc}
     * @see \Sunhill\Parser\Nodes\Node::validate()
     */
    public function validate()
    {
        $this->validateVerb();
        $this->validateFields();
        $this->validateOrder();
        $this->validateOffset();
        $this->validateLimit();
        $this->validateWhere();
        $this->validateHaving();
        $this->validateGroupBy();
    }

}