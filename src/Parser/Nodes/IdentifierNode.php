<?php
/**
 * @file IdentifierNode.php
 * A node that represents an identifier
 * Lang en
 * Reviewstatus: 2025-03-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage:
 */

namespace Sunhill\Parser\Nodes;

class IdentifierNode extends Node
{
        
    public function __construct($value)
    {
        parent::__construct('identifier',$value);
    }

    /**
     * Alias for getValue()
     */
    public function getName(): string
    {
        return $this->getValue();
    }
}
