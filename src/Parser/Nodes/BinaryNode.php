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
use Sunhill\Parser\Exceptions\TypesMismatchException;

/**
 * A binary node consists of a right and a left subtree and a connection between those
 */       
class BinaryNode extends Node
{

    use GetLowestSubtype;
    
    protected $allowed_types = [];
    
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
        foreach ($this->allowed_types as $type) {
            if (($this->typeMatch($this->left()->getDatatype(), $type->left)) && ($this->typeMatch($this->right()->getDatatype(), $type->left))) {
                return $type->resulting;
            }
        }
    }
    
    public function toString(): string
    {
        return '('.$this->left()->toString().$this->getType().$this->right()->toString().')';
    }
    
    public function addAllowedType(string $left, string $right, string $resulting)
    {
        $entry = new \stdClass();
        $entry->left = $left;
        $entry->right = $right;
        $entry->resulting = $resulting;
        $this->allowed_types[] = $entry;
    }
    
    protected function validateOperator(string $left_data_type, string $right_data_type)
    {
        foreach ($this->allowed_types as $type) {
            if (($this->typeMatch($left_data_type, $type->left)) && ($this->typeMatch($right_data_type, $type->left))) {
                return;
            }
        }
        throw new TypesMismatchException("The operator '".$this->getType()."' is not allowed for '$left_data_type' and '$right_data_type'");
    }
    
    public function validate()
    {
        $this->left()->validate();
        $this->right()->validate();
        $this->validateOperator($this->left()->getDatatype(), $this->right()->getDatatype());
    }
        
}
