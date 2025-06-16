<?php
/**
 * @file AbstractObjectExecutor.php
 * An abstract extension to the execitor of the parser
 *
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2025-06-13
 * Creation date: 2025-06-13
 * Localization: complete
 * Documentation: unknown
 * Tests:
 * Coverage Unit: 
 */

namespace Sunhill\Storage\AbstractObjectStorage;

use Sunhill\Parser\Executor;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Storage\Exceptions\NotAQueryNodeException;
use Sunhill\Query\QueryParser\QueryNode;

abstract class AbstractObjectExecutor extends Executor
{
    
    abstract protected function handleTables(QueryNode $node): mixed;
    
    abstract protected function handleWhere($query, QueryNode $node);
    
    abstract protected function handleOrder($query, QueryNode $node);
    
    abstract protected function handleOffset($query, QueryNode $node);
    
    abstract protected function handleLimit($query, QueryNode $node);
    
    abstract protected function handleGet($query);
    
    abstract protected function handleFirst($query);
    
    abstract protected function handleCount($query);
    
    protected function handleVerb($query, QueryNode $node)
    {
        switch ($node->verb()) {
            case 'get':
                return $this->handleGet($query);
            case 'first':
                return $this->handleFirst($query);
            case 'count':
                return $this->handleCount($query);
        }        
    }
    
    protected function doExecute(?Node $ast)
    {
        if (!is_a($ast, QueryNode::class)) {
            throw new NotAQueryNodeException("The given node is not a query node");
        }
        $query = $this->handleTables($ast);
        $this->handleWhere($query, $ast);
        $this->handleOrder($query, $ast);
        $this->handleOffset($query, $ast);
        $this->handleLimit($query, $ast);
        return $this->handleVerb($query, $ast);
    }
    
}