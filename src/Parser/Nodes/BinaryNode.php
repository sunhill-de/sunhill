<?php
/**
 * @file BinaryNode.php
 * A class for a binary operator
 * Lang en
 * Reviewstatus: 2025-03-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage:
 */

namespace Sunhill\Parser;

use Sunhill\Basic\Base;

/**
 * A binary node consists of a right and a left subtree and a connection between those
 */       
class BinaryNode extends Node
{

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
    
}
