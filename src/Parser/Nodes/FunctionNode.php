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
    public function __construct(string $name)
    {
        parent::__construct('func',[]);
        $this->name($name);
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

    public function getArgumentCount(): int
    {
        if (isset($this->children['arguments'])) {
            return is_a($this->children['arguments'],ArrayNode::class)?$this->children['arguments']->elementCount():1;
        }
        return 0;
    }
    
    public function getArgument(int $index): ?Node
    {
        if (!isset($this->children['arguments']) || ($index < 0) || ($index >= $this->getArgumentCount())) {
            return null;
        }
        return is_a($this->children['arguments'],ArrayNode::class)?$this->children['arguments']->getElement($index):$this->children['arguments'];
    }
}
