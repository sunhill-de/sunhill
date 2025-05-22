<?php

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

class MysqlQueryExecutor extends Base
{
    
    private function handleTables(QueryNode $node)
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
    
    private function handleWhere($query, QueryNode $node)
    {
        $this->handleSingleWhere($query, $node->getWhere());    
    }
    
    private function handleOrder($query, QueryNode $node)
    {
        $order = $node->order();
        if ($order) {
            $query->orderBy($order->field()->getChildren()['alias'].'.'.$order->field()->getName(),$order->direction());
        }
    }
    
    private function handleVerb($query, QueryNode $node)
    {
        $str = $query->toSql();
        switch ($node->verb()) {
            case 'get':
                return $query->get();
            case 'first':
                return $query->first();
            case 'count':
                return $query->count();
        }
    }
    
    private function handleOffset($query, QueryNode $node)
    {
        if ($node->offset()) {
            $query->offset($node->offset()->getValue());
        }
    }
    
    private function handleLimit($query, QueryNode $node)
    {
        if ($node->limit()) {
            $query->limit($node->limit()->getValue());
        }
        
    }
        
    public function executeQuery(QueryNode $node)
    {
        $query = $this->handleTables($node);
        $this->handleWhere($query, $node);
        $this->handleOrder($query, $node);
        $this->handleOffset($query, $node);
        $this->handleLimit($query, $node);
        return $this->handleVerb($query, $node);        
    }
}