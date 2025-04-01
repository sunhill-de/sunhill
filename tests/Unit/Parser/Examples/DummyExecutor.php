<?php

namespace Sunhill\Tests\Unit\Parser\Examples;

use Sunhill\Parser\Nodes\Node;
use Sunhill\Parser\Executor;
use Sunhill\Parser\Nodes\BinaryNode;
use Sunhill\Parser\Nodes\UnaryNode;
use Sunhill\Parser\Nodes\BooleanNode;
use Sunhill\Parser\Nodes\ArrayNode;
use Sunhill\Parser\Nodes\FloatNode;
use Sunhill\Parser\Nodes\IdentifierNode;
use Sunhill\Parser\Nodes\IntegerNode;
use Sunhill\Parser\Nodes\FunctionNode;
use Sunhill\Parser\Nodes\StringNode;
use Sunhill\Query\QueryParser\QueryNode;

class DummyExecutor extends Executor
{
    
    protected function doExecute(?Node $ast)
    {
        if (is_null($ast)) {
            return "";
        }
        switch ($ast::class) {
            case ArrayNode::class:
                $result = '[';
                $first = true;
                for ($i=0;$i<$ast->getElementCount();$i++) {
                    $result .= ($first?"":",").$this->doExecute($ast->getElement($i));
                }
                return $result."]";
            case BinaryNode::class:
                return '('.$this->doExecute($ast->left()).')'.$ast->getType().'('.$this->doExecute($ast->right()).')';
            case BooleanNode::class:
                return $ast->getValue()?"true":"false";
            case FloatNode::class:
            case IntegerNode::class:
                return strval($ast->getValue());
            case IdentifierNode::class:
                return $ast->getValue();
            case FunctionNode::class:
                return $ast->name().'('.$this->doExecute($ast->arguments()).')';
            case QueryNode::class:
                return "where:[".$this->doExecute($ast->getWhere())."],".
                       "order:[".$this->doExecute($ast->order())."],".
                       "group:[".$this->doExecute($ast->group())."],".
                       "offset:[".$this->doExecute($ast->offset())."],".
                       "limit:[".$this->doExecute($ast->limit())."]";
            case StringNode::class:
                return '"'.$ast->getValue().'"';
            case UnaryNode::class:    
                return $ast->getType().'('.$this->doExecute($ast->child()).')';
            default:
                return "unknown Node: ".$ast::class;
        }
    }

}