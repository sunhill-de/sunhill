<?php
/**
 * @file ReferenceNode.php
 * A node that represents an identifier pointing to a subfield
 * Lang en
 * Reviewstatus: 2025-06-29
 * Create date: 2025-06-29
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage Unit: 
 */

namespace Sunhill\Parser\Nodes;

use Sunhill\Parser\Traits\UnknownDatatype;

class ReferenceNode extends TerminalNode
{
  
    use UnknownDatatype;
    
    public function __construct($value)
    {
        parent::__construct('reference',$value);
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
    
}
