<?php

/**
 * @file CantProcessPropertyException.php
 * Provides the CantProcessPropertyException that is raised, when addElement() was called with a parameter
 * that could not be solved to a property
 * 
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: none
 * Documentation: complete
 * Tests: none
 * Coverage: unknown
 */

namespace Sunhill\Properties\Exceptions;

/**
 * An exception that is raised, when a read or write attemt is performed with no set storage
 * @author lokal
 */
class CantProcessPropertyException extends PropertyException 
{
}
