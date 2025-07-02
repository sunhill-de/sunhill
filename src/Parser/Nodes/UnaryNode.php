<?php
/**
 * @file UnaryNode.php
 * A basic class for a node that has only one child
 * Lang en
 * Reviewstatus: 2025-06-27
 * Create date: 2025-03-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage Unit: 100 % (2025-06-06)
 */

namespace Sunhill\Parser\Nodes;

use Sunhill\Basic\Base;

class UnaryNode extends Node
{
        
    public function __construct(string $type)
    {
        parent::__construct($type,[]);
    }

    /**
     * Simplified setter/getter for the child. When called with parameter it acts as a setter otherwise as a getter.
     */              
    public function child(?Node $node = null): Node
    {
        if (!is_null($node)) {
            $this->children['child'] = $node;
            return $this;
        } else {
            return $this->children['child'];
        }
    }
 
    /**
     * Returns the datatype of the child node
     * 
     * {@inheritDoc}
     * @see \Sunhill\Parser\Nodes\Node::getDatatype()
     */
    public function getDatatype(): ?string
    {
        return $this->child()->getDatatype();
    }
 
    public function toString(): string
    {
        return '('.$this->getType().$this->child()->toString().')';
    }
        
    
    public function validate(): bool
    {
        return true; // @todo implement me
    }
}
