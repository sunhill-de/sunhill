<?php

/**
 * @file TypeDateTime.php
 * Defines a type for datetime fields and a ancestor for date and time fields
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\Types;

use Sunhill\Properties\AbstractSimpleProperty;

class TypeDateTime extends AbstractSimpleProperty
{
   
    
    /**
     * First check if the given value is an ingteger at all all. afterwards check the boundaries
     * 
     * {@inheritDoc}
     * @see Sunhill\\ValidatorBase::isValid()
     */
    public function isValid($input): bool
    {
        if (is_a($input, \DateTime::class)) {
            return true;
        }
        if (is_numeric($input)) {
            $input = '@'.$input;
        }
        if (empty($input)) {
            return false;
        }
        try {
            $date = new \DateTime($input);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
    
    /**
     * Cuts the input string to a maximum length
     * 
     * {@inheritDoc}
     * @see Sunhill\\ValidatorBase::doConvertToInput()
     */
    protected function formatFromInput($input)
    {
        if (is_a($input, \DateTime::class)) {
            return $input;
        }
        if (is_numeric($input)) {
            $input = '@'.$input;
        }
        return new \DateTime($input);        
    }
    
    /**
     * The storage stores a datetime as a string in the form 'Y-m-d H:i:s'
     *
     * @param unknown $input
     * @return unknown, by dafult just return the value
     */
    protected function formatForStorage($input)
    {
        return $input->format('Y-m-d H:i:s');
    }
    
    protected function formatForHuman($input)
    {
        return $input->format('j.n.Y H:i:s');
    }
    
    /**
     * Sometimen the value was stored in the storage in another format than it is in the property
     *
     * @param unknown $output
     * @return unknown,. by default just pass the value
     */
    protected function formatFromStorage($output)
    {
        return new \DateTime($output);
    }
    
    public function getAccessType(): string
    {
        return 'datetime';
    }
    
    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'datetime');
        static::addInfo('description', 'The basic type datetime.', true);
        static::addInfo('type', 'basic');
    }
    
}