<?php

namespace Sunhill\Query\QueryParser;

use Sunhill\Parser\Nodes\Node;

class OrderNode extends Node
{
    public function __construct()
    {
        parent::__construct('order',['field'=>null,'direction'=>'asc']);   
    }
    
    public function field(?Node $node = null)
    {
        return $this->handleReplacingChild('field', $node);
    }

    public function direction(?string $direction = null)
    {
        return $this->handleReplacingChild('direction', $direction);
    }
}