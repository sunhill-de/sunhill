<?php
/**
 * @file ReferenceNode.php
 * A node that represents an identifier pointing to a subfield
 * Lang en
 * Reviewstatus: 2025-07-15
 * Create date: 2025-06-29
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/Nodes/ReferenceNodeTest.php
 * Coverage Unit: 
 */

namespace Sunhill\Parser\Nodes;

use Sunhill\Parser\Traits\UnknownDatatype;
use Sunhill\Query\Exceptions\InvalidStatementException;

class ReferenceNode extends TerminalNode
{
  
    use UnknownDatatype;
    
    public function __construct($value, ?Node $reference = null)
    {
        parent::__construct('reference',$value);
        if ($reference) {
            $this->reference($reference);
        }
    }
    
    /**
     * Alias for getValue()
     */
    public function getName(): string
    {
        return $this->getValue();
    }
    
    /**
     * Getter and setter for reference
     * 
     * @param Node $reference
     * @return \Sunhill\Parser\Nodes\ReferenceNode|NULL|mixed
     */
    public function reference(?Node $reference = null)
    {
        return $this->handleReplacingChild('reference', $reference);
    }    
    
    /**
     * Checks if this reference is valid. First checks if there is a reference at all, if yes call validate of the reference
     * 
     * {@inheritDoc}
     * @see \Sunhill\Parser\Nodes\Node::validate()
     */
    public function validate()
    {
        if (!$this->reference()) {
            throw new InvalidStatementException("No reference is set.");
        }
        $this->reference()->validate();
    }
    
    /**
     * Converte the reference to a string
     * 
     * {@inheritDoc}
     * @see \Sunhill\Parser\Nodes\Node::toString()
     */
    public function toString(): string
    {
        return $this->getName().'->'.$this->reference()->toString();        
    }
    
    /**
     * Returns the datatype of the reference
     * 
     * {@inheritDoc}
     * @see \Sunhill\Parser\Nodes\Node::getDatatype()
     */
    public function getDatatype(): ?string
    {
        if ($this->reference()) {
            return $this->reference()->getDatatype();
        }
        return null;
    }
}
