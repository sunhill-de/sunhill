<?php
/**
 * @file InvalidIDException.php
 * Provides the InvalidIDException
 * Lang en
 * Reviewstatus: 2024-10-12
 * Localization: none
 * Documentation: complete
 */

namespace Sunhill\Storage\Exceptions;

/**
 * This exception is thrown when a load() of a persistent storage was called with an invalid
 * id (or no id at all if thats not allowed)
 * 
 * @author klaus
 *
 */
class InvalidIDException extends StorageException {}