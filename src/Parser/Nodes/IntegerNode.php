<?php
/**
 * @file IntegerNode.php
 * A node that represents an integer constant
 * Lang en
 * Reviewstatus: 2025-03-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage:
 */

namespace Sunhill\Parser\Nodes;

class IntegerNode extends Node
{
        
    public function __construct($value)
    {
        parent::__construct('integer',['value'=>$value);
    }

}
