<?php
/**
 * @fileAbstractAttributeStorage.php
 * Provides the basic class for attributes
 * Lang en
 * Reviewstatus: 2024-05-29
 * Create date: 2025-05-29
 * Localization: complete
 * Documentation: complete
 *
 * Tests: 
 * Coverage Unit:
 */
namespace Sunhill\Attributes;

use Sunhill\Basic\Base;

abstract class AbstractAttributeStorage extends Base
{
    
    abstract public function loadAttribute(int $container_id, int $attribute_id);
    
    abstract public function searchAttribute(string $name): ?\stdClass;
}