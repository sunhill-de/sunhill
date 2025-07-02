<?php
/**
 * @file DateNode.php
 * A node that represents a date constant
 * Lang en
 * Reviewstatus: 2025-03-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage Unit: 100 % (2025-06-06)
 */

namespace Sunhill\Parser\Nodes;

class DateNode extends TerminalNode
{
        
    public function __construct($value)
    {
        parent::__construct('date',$value);
    }

    public function getDatatype(): ?string
    {
        return 'date';
    }
    
    public function toString(): string
    {
        return '"'.$this->getValue().'"';
    }
    
    public function validate(): bool
    {
        return true; // Constants are always valid
    }
    
}
