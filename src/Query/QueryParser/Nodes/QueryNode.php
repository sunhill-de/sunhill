<?php
/**
 * @file QueryNode.php
 * A node that represents all informations about a query. That includes fields, wheres, groups, havings, 
 * orders, offset and limit
 * Lang en
 * Reviewstatus: 2025-07-07
 * Creation date: 2025-04-25
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Query/QueryParser/Nodes/QueryNodeTest.php
 * Coverage Unit:
 */

namespace Sunhill\Query\QueryParser\Nodes;

use Sunhill\Parser\Nodes\Node;
use Sunhill\Query\Exceptions\InvalidStatementException;

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
     * certain fields of a record and not the whole record
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
    
    public function order(?Node $node = null)
    {
        return $this->handleOptionalArrayChild('order', $node);
    }
    
    public function group(?Node $node = null)
    {
        return $this->handleOptionalArrayChild('group', $node);
    }
    
    public function where(?Node $node = null): ?Node
    {
        return $this->handleReplacingChild('where_conditions', $node);
    }
    
    public function getWhere(): ?Node
    {
        return isset($this->children['where_conditions'])?$this->children['where_conditions']:null;        
    }

    public function setWhere(Node $node): static
    {
        $this->children['where_conditions'] = $node;
        
        return $this;
    }
    
    public function addStorage(string $storage_name, string $type = 'inner', string $target_alias = 'a', string $field = 'id'): string
    {
        $alias = $this->next_storage_alias++;
        $this->storages[$alias] = new \stdClass();
        $this->storages[$alias]->storage = $storage_name;
        $this->storages[$alias]->join = $type;
        $this->storages[$alias]->alias = $target_alias;
        $this->storages[$alias]->field = $field;
                
        return $alias;
    }
    
    public function getStorages(): array
    {
        return $this->storages;
    }
    
    public function toString(): string
    {
        switch ($this->verb()) {
            case 'get':
            case 'first':    
                $result = 'SELECT';
                break;
        }
    }

    private function validateOptionalNull(?Node $node)
    {
        if (!is_null($node)) {
            $node->validate();
        }
    }
    
    public function validate(): bool
    {
        if (!in_array(strtolower($this->verb()),['select','first','count','update','delete','insert'])) {
            throw new InvalidStatementException("The verb '".$this->verb()."' is not valid");
        }
        $this->validateOptionalNull($this->fields());
        if ($this->offset() < 0) {
            throw new InvalidStatementException("Offset must be a positive integer (or 0)");
        }
        if ($this->limit() < 1) {
            throw new InvalidStatementException("Limit must be a positiv numer greater than 0");
        }
    }

}