<?php
/**
 * @file ArrayNode.php
 * A node that represents an array of nodes
 * Lang en
 * Reviewstatus: 2025-03-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage:
 */

namespace Sunhill\Parser\Nodes;

class ArrayNode extends Node
{
        
    public function __construct($first_element)
    {
        parent::__construct('array',['values'=>[$first_element]]);
    }

    public function addElement(Node $element): static
    {
        $this->children['values'][] = $element;
        
        return $this;
    }
    
    public function getElement(int $index): ?Node
    {
        if (($index >= 0) && ($index < $this->elementCount())) {
            return $this->children['values'][$index];
        }
        return null;
    }
    
    public function elementCount(): int
    {
        return count($this->children['values']);
    }
}
