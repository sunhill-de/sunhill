<?php
/**
 * @file StringNode.php
 * A node that represents a string constant
 * Lang en
 * Reviewstatus: 2025-03-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage:
 */

namespace Sunhill\Parser\Nodes;

class StringNode extends TerminalNode
{
        
    public function __construct($value)
    {
        parent::__construct('string',$value);
    }

}
