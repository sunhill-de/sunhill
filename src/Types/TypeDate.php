<?php

/**
 * @file TypeDate.php
 * Defines a type for datetime fields and a ancestor for date and time fields
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\Properties\Types;

class TypeDate extends TypeDateTime
{
       
    /**
     * The storage stores a datetime as a string in the form 'Y-m-d H:i:s'
     *
     * @param unknown $input
     * @return unknown, by dafult just return the value
     */
    protected function formatForStorage($input)
    {
        return $input->format('Y-m-d');
    }
    
    protected function formatForHuman($input)
    {
        return $input->format('j.n.Y');    
    }
    
    public function getAccessType(): string
    {
        return 'date';
    }

    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'date');
        static::addInfo('description', 'The basic type date.', true);
        static::addInfo('type', 'basic');
    }
    
}