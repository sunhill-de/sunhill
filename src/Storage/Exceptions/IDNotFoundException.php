<?php
/**
 * @file IDNotFoundException.php
 * Provides the IDNotFoundException
 * Lang en
 * Reviewstatus: 2024-10-12
 * Localization: none
 * Documentation: complete
 */

namespace Sunhill\Storage\Exceptions;

/**
 * This exception is raised when load() was called with a valid id that does not exists.
 * @author klaus
 *
 */
class IDNotFoundException extends StorageException
{
    
}