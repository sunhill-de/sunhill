<?php
/**
 * @file TimeNode.php
 * A node that represents a time constant
 * Lang en
 * Reviewstatus: 2025-03-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage: 100 % (2025-06-06)
 */

namespace Sunhill\Parser\Nodes;

class TimeNode extends TerminalNode
{
        
    public function __construct($value)
    {
        parent::__construct('time',$value);
    }

}
