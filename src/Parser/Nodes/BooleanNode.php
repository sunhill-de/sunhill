<?php
/**
 * @file BooleanNode.php
 * A node that represents a boolean constant
 * Lang en
 * Reviewstatus: 2025-03-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage:
 */

namespace Sunhill\Parser\Nodes;

class BooleanNode extends Node
{
        
    public function __construct($value)
    {
        parent::__construct('boolean',$value);
    }

}
