<?php
/**
 * @file IdentifierNode.php
 * A node that represents an identifier
 * Lang en
 * Reviewstatus: 2025-06-02
 * Create date: 2025-03-17
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage Unit: 
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
    
    /**
     * Just return the name of the identifier
     * 
     * {@inheritDoc}
     * @see \Sunhill\Parser\Nodes\Node::toString()
     */
    public function toString(): string
    {
        return $this->getName();
    }
    
    /**
     * A identifier is valid if it exists. This function does not check if the value fits to the type
     * 
     * {@inheritDoc}
     * @see \Sunhill\Parser\Nodes\Node::validate()
     */
    public function validate(): bool
    {
        return !is_null($this->datatype); // @todo Implement me
    }
    
}
