<?php
/**
 * @file BinaryNode.php
 * A class for a binary operator
 * Lang en
 * Reviewstatus: 2025-03-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage Unit: 100 % (2025-06-06)
 */

namespace Sunhill\Parser\Nodes;

use Sunhill\Basic\Base;
use Sunhill\Parser\Traits\GetLowestSubtype;

/**
 * A binary node consists of a right and a left subtree and a connection between those
 */       
class BinaryNode extends Node
{

    use GetLowestSubtype;
    
    /**
     * The constructor is passed the operator for this binary node. The left and right subtree is set 
     * via left() and right()
     */   
    public function __construct(string $type)
    {
        parent::__construct($type,[]);
    }

    /**
     * Simplified setter/getter for the left node. When called with parameter it acts as a setter otherwise as a getter.
     */   
    public function left(?Node $left_node = null): Node
    {
        if (!is_null($left_node)) {
            $this->children['left'] = $left_node;
            return $this;
        } else {
            return $this->children['left'];
        }
    }
    
    /**
     * Simplified setter/getter for the right node. When called with parameter it acts as a setter otherwise as a getter.
     */   
    public function right(?Node $right_node = null): Node
    {
        if (!is_null($right_node)) {
            $this->children['right'] = $right_node;
            return $this;
        } else {
            return $this->children['right'];
        }
    }
    
    public function getDatatype(): ?string
    {
        return $this->getLowestSubtype($this->left()->getDatatype(), $this->right()->getDatatype());        
    }
    
    public function toString(): string
    {
        return '('.$this->left()->toString().$this->getType().$this->right()->toString().')';
    }
    
    protected function validateOperator(string $operator, string $left_data_type, string $right_data_type)
    {
        
    }
    
    public function validate()
    {
        $this->left()->validate();
        $this->right()->validate();
        $this->validateOperator($this->getType(), $this->left()->getDatatype(), $this->right()->getDatatype());
    }
        
}
