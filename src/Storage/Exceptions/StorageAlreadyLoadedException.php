<?php
/**
 * @file StorageAlreadyLoadedException.php
 * Provides the StorageAlreadyLoadedException
 * Lang en
 * Reviewstatus: 2024-10-12
 * Localization: none
 * Documentation: complete
 */

namespace Sunhill\Storage\Exceptions;

/**
 * This exception is thrown when a load() of a persistent storage was called and the storage
 * was already loaded
 * 
 * @author klaus
 *
 */
class StorageAlreadyLoadedException extends StorageException {}