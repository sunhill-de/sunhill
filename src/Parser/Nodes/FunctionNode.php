<?php
/**
 * @file FunctionNode.php
 * A basic class for a node that is a function
 * Lang en
 * Reviewstatus: 2025-03-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage:
 */

namespace Sunhill\Parser\Nodes;

class FunctionNode extends Node
{

    /**
     * Simplyfied constructor that just fills the parent with default values
     */   
    public function __construct()
    {
        parent::__construct('func',[]);
    }

    /**
     * Simplified setter/getter for the function name. When called with parameter it acts as a setter otherwise as a getter.
     */      
    public function name(?string $name = null)
    {
        if (!is_null($name)) {
            $this->children['name'] = $name;
            return $this;
        } else {
            return $this->children['name'];
        }
    }
    
    /**
     * Simplified setter/getter for the function arguments. When called with parameter it acts as a setter otherwise as a getter.
     */      
    public function arguments(?Node $arguments = null)
    {
        if (!is_null($arguments)) {
            $this->children['arguments'] = $arguments;
            return $this;
        } else {
            return $this->children['arguments']??null;
        }
    }
    
}
