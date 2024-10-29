<?php

/**
 * @file NoStorageSetException.php
 * Throws the NoStorageSetException
 * Lang en
 * Reviewstatus: 2024-10-08
 * Localization: none
 * Documentation: complete
 * Tests: none
 * Coverage: unknown
 */

namespace Sunhill\Properties\Exceptions;

/**
 * This storage is thrown when a read or write attempt takes place on a property where
 * no storage was defined. 
 * @author lokal
 */
class NoStorageSetException extends PropertyException 
{
}
