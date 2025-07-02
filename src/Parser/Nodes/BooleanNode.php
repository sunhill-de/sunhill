<?php
/**
 * @file BooleanNode.php
 * A node that represents a boolean constant
 * Lang en
 * Reviewstatus: 2025-03-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage Unit:100 % (2025-06-06)
 */

namespace Sunhill\Parser\Nodes;

class BooleanNode extends TerminalNode
{
        
    public function __construct($value)
    {
        parent::__construct('boolean',$value);
    }

    public function getDatatype(): ?string
    {
        return 'boolean';
    }
    
    public function toString(): string
    {
        return $this->getValue()?'true':'false';        
    }
}
