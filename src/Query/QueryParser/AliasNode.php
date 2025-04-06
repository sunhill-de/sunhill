<?php

namespace Sunhill\Query\QueryParser;

use Sunhill\Parser\Nodes\Node;

class AliasNode extends Node
{
    public function __construct($expression, $alias)
    {
        parent::__construct('alias',['expression'=>$expression,'alias'=>$alias]);
    }
    
    public function expression(?Node $node = null)
    {
        return $this->handleReplacingChild('expression', $node);
    }
    
    public function alias(?Node $node = null)
    {
        return $this->handleReplacingChild('alias', $node);
    }
}