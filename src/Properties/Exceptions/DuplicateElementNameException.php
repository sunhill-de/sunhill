<?php

/**
 * @file DuplicateElementNameException.php
 * Provides the DuplicateElementNameException that is raised, the given name was already used for a 
 * property
 * 
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: none
 * Documentation: complete
 * Tests: none
 * Coverage: unknown
 */

namespace Sunhill\Properties\Properties\Exceptions;

/**
 * An exception that is raised, when a read or write attemt is performed with no set storage
 * @author lokal
 */
class DuplicateElementNameException extends PropertyException 
{
}
