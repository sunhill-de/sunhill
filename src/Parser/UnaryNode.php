<?php
/**
 * @file UnaryNode.php
 * A basic class for a node that has only one child
 * Lang en
 * Reviewstatus: 2025-03-03
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage:
 */

namespace Sunhill\Parser;

use Sunhill\Basic\Base;

class UnaryNode extends Node
{
        
    public function __construct(string $type)
    {
        parent::__construct($type,[]);
    }
    
    public function child(?Node $node = null): Node
    {
        if (!is_null($node)) {
            $this->children['child'] = $node;
            return $this;
        } else {
            return $this->children['child'];
        }
    }
        
}