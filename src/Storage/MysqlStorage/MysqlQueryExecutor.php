<?php
/**
 * @file MysqlQuerExecutor.php
 * An executor for generalized queries in this case for mysql stored objects 
 *
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2025-05-28
 * Create date: 2025-05-28
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage Unit: 95.16  (2025-06-06)
 * PSR-State: completed
 */

namespace Sunhill\Storage\MysqlStorage;

use Sunhill\Basic\Base;
use Sunhill\Query\QueryParser\QueryNode;
use Illuminate\Support\Facades\DB;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Parser\Nodes\StringNode;
use Sunhill\Parser\Nodes\FloatNode;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Parser\Nodes\DateNode;
use Sunhill\Parser\Nodes\DateTimeNode;
use Sunhill\Parser\Nodes\TimeNode;
use Sunhill\Query\QueryParser\AliasNode;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Parser\Nodes\Node;
use Sunhill\Parser\Executor;
use Sunhill\Storage\AbstractObjectStorage\AbstractObjectExecutor;

class MysqlQueryExecutor extends AbstractObjectExecutor
{
    
    protected function handleTables(QueryNode $node): mixed
    {
        $query = DB::table('objects as a')->select('a.id');
        foreach ($node->getStorages() as $alias => $storage) {
            switch ($storage->join) {
                case 'inner':
                    $query->join($storage->storage.' as '.$alias, $storage->alias.'.'.$storage->field, '=', $alias.'.id');
                    break;
                case 'left':                    
                    $query->leftJoin($storage->storage.' as '.$alias, $storage->alias.'.'.$storage->field, '=', $alias.'.id');
                    break;
            }
        }
        return $query;
    }
    
    private function handleBinaryNode($query, BinaryNode $node)
    {
        switch ($node->getType()) {
            case '=':
            case '<>':
            case '>':
            case '<':
            case '>=':
            case '<=':
                $query->where($this->handleSingleWhere($query, $node->left()),$node->getType(),$this->handleSingleWhere($query, $node->right()));
                break;
            case '&&':
                $this->handleSingleWhere($query, $node->left());
                $query->where(function($subquery) use ($node)
                {
                    $this->handleSingleWhere($subquery, $node->right());
                });
                break;
            case '||':
                $this->handleSingleWhere($query, $node->left());
                $query->orWhere(function($subquery) use ($node)
                {
                    $this->handleSingleWhere($subquery, $node->right());
                });
                break;
        }
    }
    
    private function handleSingleWhere($query, ?Node $node)
    {
        if (is_null($node)) {
            return;
        }
        switch ($node::class) {
            case IdentifierNode::class:
                return $node->getName();
            case IntegerNode::class:
            case FloatNode::class:
            case StringNode::class:
            case DateNode::class:
            case TimeNode::class:
            case DateTimeNode::class:
                return $node->getValue();
            case AliasNode::class:
                return $node->alias().'.'.$this->handleSingleWhere($query, $node->expression());
            case BinaryNode::class:
                return $this->handleBinaryNode($query, $node);
        }
    }
    
    protected function handleWhere($query, QueryNode $node)
    {
        $this->handleSingleWhere($query, $node->getWhere());    
    }
    
    protected function handleOrder($query, QueryNode $node)
    {
        $order = $node->order();
        if ($order) {
            $query->orderBy($order->field()->getChildren()['alias'].'.'.$order->field()->getName(),$order->direction());
        }
    }
    
    protected function handleGet($query)
    {
        return $query->get();        
    }
    
    protected function handleFirst($query)
    {
        return $query->first();        
    }
    
    protected function handleCount($query)
    {
        return $query->count();        
    }
    
    protected function handleOffset($query, QueryNode $node)
    {
        if ($node->offset()) {
            $query->offset($node->offset()->getValue());
        }
    }
    
    protected function handleLimit($query, QueryNode $node)
    {
        if ($node->limit()) {
            $query->limit($node->limit()->getValue());
        }
        
    }
            
}