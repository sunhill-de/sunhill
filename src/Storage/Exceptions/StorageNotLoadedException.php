<?php
/**
 * @file StorageNotLoadedException.php
 * Provides the StorageNotLoadedException
 * Lang en
 * Reviewstatus: 2024-10-12
 * Localization: none
 * Documentation: complete
 */

namespace Sunhill\Storage\Exceptions;

/**
 * This exception is thrown when a reading or writing access is performed and the storage was
 * not loaded before
 * 
 * @author klaus
 *
 */
class StorageNotLoadedException extends StorageException {}