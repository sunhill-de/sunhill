<?php
/**
 * @file FloatNode.php
 * A node that represents an float constant
 * Lang en
 * Reviewstatus: 2025-03-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage:
 */

namespace Sunhill\Parser\Nodes;

class FloatNode extends TerminalNode
{
        
    public function __construct($value)
    {
        parent::__construct('float',$value);
    }

}
