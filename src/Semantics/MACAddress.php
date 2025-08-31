<?php

/**
 * @file MACAddress.php
 * A semantic class for a string that is the mac address of a network device
 * Lang en
 * Reviewstatus: 2024-10-09
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Semantic/SemanticTest.php
 * Coverage Unit: 66.67 % (2025-06-06)
 */

namespace Sunhill\Semantics;

class MACAddress extends NetworkAddress
{
    /**
     * Returns the unique id string for the semantic of this property
     */
    public static function getSemantic(): string
    {
        return 'mac_address';
    }

    /**
     * The storage stores a mac address in lower case
     *
     * @param  unknown  $input
     * @return unknown, by dafult just return the value
     */
    protected function formatForStorage($input)
    {
        return strtolower($input);
    }

    /**
     * First check if the given value is an ingteger at all all. afterwards check the boundaries
     *
     * {@inheritDoc}
     *
     * @see Sunhill\\\ValidatorBase::isValid()
     */
    public function isValid($input): bool
    {
        return filter_var($input, FILTER_VALIDATE_MAC);
    }

    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'macaddress');
        static::addInfo('description', 'The mac address of an network device.', true);
        static::addInfo('type', 'semantic');
    }
}
