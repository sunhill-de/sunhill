<?php
/**
 * @file StructureNeededException.php
 * Provides the StructureNeededException
 * Lang en
 * Reviewstatus: 2024-10-09
 * Localization: none
 * Documentation: complete
 */

namespace Sunhill\Storage\Exceptions;

use Sunhill\Exceptions\SunhillException;

/**
 * Is thrown when a structure of the owning property is needed but not provided
 * @author klaus
 *
 */
class StructureNeededException extends StorageException {}