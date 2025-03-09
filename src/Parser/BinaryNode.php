<?php
/**
 * @file Node.php
 * A basic class for a node as a parsing result
 * Lang en
 * Reviewstatus: 2025-03-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage:
 */

namespace Sunhill\Parser;

use Sunhill\Basic\Base;

class BinaryNode extends Node
{
        
    public function __construct(string $type)
    {
        parent::__construct($type,[]);
    }
    
    public function left(?Node $left_node = null): Node
    {
        if (!is_null($left_node)) {
            $this->children['left'] = $left_node;
            return $this;
        } else {
            return $this->children['left'];
        }
    }
    
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