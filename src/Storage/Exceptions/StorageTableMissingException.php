<?php
/**
 * @file StorageTableMissingException.php
 * Provides the StorageTableMissingExceptionException
 * Lang en
 * Reviewstatus: 2024-10-12
 * Localization: none
 * Documentation: complete
 */

namespace Sunhill\Storage\Exceptions;

/**
 * This exception is thrown when a mysql storage expects a table that does not exist.
 * 
 * @author klaus
 *
 */
class StorageTableMissingException extends StorageException {}