<?php
/**
 * @file IdentifierNode.php
 * A node that represents an identifier
 * Lang en
 * Reviewstatus: 2025-03-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage Unit: 100 % (2025-06-06)
 */

namespace Sunhill\Parser\Nodes;

use Sunhill\Parser\Traits\UnknownDatatype;

class IdentifierNode extends TerminalNode
{

    use UnknownDatatype;
    
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
    
    public function reference(?Node $reference = null)
    {
        return $this->handleReplacingChild('reference', $reference);
    }
    
    public function parent(?Node $parent = null)
    {
        return $this->handleReplacingChild('parent', $parent);
    }
}
