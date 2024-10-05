<?php
/**
 * @file Bistate.php
 * Defines an abstract for values that can handle two values (like "on" and "off") 
 * Lang de,en
 * Reviewstatus: 2024-03-01
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\Semantics;

use Sunhill\Types\TypeBoolean;

abstract class Bistate extends TypeBoolean
{
    
    /**
     * Returns the value that represents the true state
     * 
     * @return string
     */
    abstract protected function getTrueValue(): string;
    
    /**
     * Returns the value that represents the true state
     *
     * @return string
     */
    abstract protected function getFalseValue(): string;
    
    /**
     * Depending on the given value return the according value
     * 
     * {@inheritDoc}
     * @see Sunhill\\\Types\TypeBoolean::formatForHuman()
     */
    protected function formatForHuman($input)
    {
        if ($input) {
            return $this->getTrueValue();
        } else {
            return $this->getFalseValue();            
        }
    }
}