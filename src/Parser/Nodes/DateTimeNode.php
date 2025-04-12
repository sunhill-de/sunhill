<?php
/**
 * @file DateTimeNode.php
 * A node that represents a date-time constant
 * Lang en
 * Reviewstatus: 2025-03-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage:
 */

namespace Sunhill\Parser\Nodes;

class DateTimeNode extends TerminalNode
{
        
    public function __construct($value)
    {
        parent::__construct('datetime',$value);
    }

}
