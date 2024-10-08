<?php
/**
 * @file sunhill_helpers.php
 * A collection of gobally avaiable functions that are useful in the sunhill framework
 *
 * @author Klaus Dimde
 * ----------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2024-10-05
 * Localization: not needed
 * Documentation: all public
 * Wiki: /Little_helper
 * Tests: Unit/InfoMarket/Marketeer.php
 * Coverage: unknown
 * PSR-State: complete
 */

/**
 * Creates a stdClass out of an associated array.
 * 
 * @param array $values
 * @return \StdClass
 * 
 * @wiki: /Little_helper#makeStdClass()
 */
function makeStdclass(array $values): \StdClass
{
    $result = new \StdClass();
    foreach ($values as $key => $value) {
        $result->$key = $value;
    }
    return $result;
}