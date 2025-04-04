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
use Sunhill\Query\QueryParser\OrderNode;

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
                for ($i=0;$i<$ast->elementCount();$i++) {
                    $result .= ($first?"":",").$this->doExecute($ast->getElement($i));
                    $first = false;
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
                $parent = $ast->parent();
                $reference = $ast->reference();                
                return (is_null($reference)?'':'{'.$this->doExecute($reference).'}.').(is_null($parent)?'':'{'.$this->doExecute($parent).'}->').$ast->getValue();
            case FunctionNode::class:
                return $ast->name().'({'.$this->doExecute($ast->arguments()).'})';
            case OrderNode::class:
                return $this->doExecute($ast->field()).' '.$ast->direction();
            case QueryNode::class:
                return $ast->verb().','.
                       "fields:[".$this->doExecute($ast->fields())."],".
                       "where:[".$this->doExecute($ast->getWhere())."],".
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