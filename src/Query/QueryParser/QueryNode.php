<?php

namespace Sunhill\Query\QueryParser;

use Sunhill\Parser\Nodes\Node;

class QueryNode extends Node
{
    protected $storages = [];
    
    protected $next_storage_alias = 'b';
    
    public function __construct()
    {
        parent::__construct('query',['fields'=>null,'verb'=>'select','offset'=>null,'limit'=>null,'order'=>null,'group'=>null,'where_condition'=>null]);   
    }
    
    public function verb(?string $verb = null)
    {
        return $this->handleReplacingChild('verb', $verb);
    }

    public function fields(?Node $fields = null)
    {
        return $this->handleOptionalArrayChild('fields', $fields);    
    }
    
    public function offset(?Node $node = null)
    {
        return $this->handleReplacingChild('offset', $node);
    }
    
    public function limit(?Node $node = null)
    {
        return $this->handleReplacingChild('limit', $node);
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
}