<?php
/**
 * @file AttributeNameNotSetException.php
 * Provides the AttributeNameNotSetException
 * Lang en
 * Reviewstatus: 2024-12-18
 * Localization: none
 * Documentation: complete
 */

namespace Sunhill\Storage\Exceptions;

use Sunhill\Basic\SunhillException;

/**
 * This exception is thrown when load() on an attribute is called and no attribute name is set
 *
 * @author klaus
 *
 */
class AttributeNameNotSetException extends StorageException {}