<?php
/**
 * @file DateNode.php
 * A node that represents a date constant
 * Lang en
 * Reviewstatus: 2025-03-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage:
 */

namespace Sunhill\Parser\Nodes;

class DateNode extends TerminalNode
{
        
    public function __construct($value)
    {
        parent::__construct('date',$value);
    }

}
