<?php

namespace Sunhill\Query\Helpers;

use Sunhill\Parser\Nodes\Node;

class QueryNode extends Node
{
    public function __construct()
    {
        parent::__construct('query',['verb'=>'select','offset'=>null,'limit'=>null,'order'=>null,'group'=>null,'where'=>null]);   
    }
    
    public function verb(?string $verb = null)
    {
        $this->handleReplacingChild('verb', $node);
    }
    
    public function offset(?Node $node = null)
    {
        $this->handleReplacingChild('offset', $node);
    }
    
    public function limit(?Node $node = null)
    {
        $this->handleReplacingChild('limit', $node);
    }
    
    public function order(?Node $node = null)
    {
        $this->handleOptionalArrayChild('order', $node);
    }
    
    public function group(?Node $node = null)
    {
        $this->handleOptionalArrayChild('group', $node);
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
}