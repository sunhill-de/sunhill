<?php
/**
 * @file FunctionNode.php
 * A basic class for a node that has is a function
 * Lang en
 * Reviewstatus: 2025-03-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage:
 */

namespace Sunhill\Parser;

class FunctionNode extends Node
{
        
    public function __construct()
    {
        parent::__construct('func',[]);
    }
    
    public function name(?string $name = null)
    {
        if (!is_null($name)) {
            $this->children['name'] = $name;
            return $this;
        } else {
            return $this->children['name'];
        }
    }
    
    public function arguments(?Node $arguments = null)
    {
        if (!is_null($arguments)) {
            $this->children['arguments'] = $arguments;
            return $this;
        } else {
            return $this->children['arguments'];
        }
    }
    
}