<?php
/**
 * @file StringNode.php
 * A node that represents a string constant
 * Lang en
 * Reviewstatus: 2025-03-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage Unit: 100 % (2025-06-06)
 */

namespace Sunhill\Parser\Nodes;

class StringNode extends TerminalNode
{
        
    public function __construct($value)
    {
        parent::__construct('string',$value);
    }

    public function getDatatype(): ?string
    {
        return 'string';
    }
 
    public function toString(): string
    {
        return '"'.$this->getValue().'"';
    }
    
 }
