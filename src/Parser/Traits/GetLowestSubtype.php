<?php
/**
 * @file GetLowestSubtype.php
 * A trait for nodes that have to compare datatype to detect a resulting datatype
 * Lang en
 * Reviewstatus: 2025-06-27
 * Create date: 2025-06-27
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Parser/NodeTest.php
 * Coverage Unit:
 */

namespace Sunhill\Parser\Traits;

trait GetLowestSubtype
{
    
    protected function getLowestSubtype(?string $first, ?string $second): ?string
    {
        if ($first == $second) {
            return $first;
        }
        if (($first == 'mixed') || ($second == 'mixed')) {
            return 'mixed';
        }
        if ((($first == 'integer') && ($second == 'float')) ||
            (($first == 'float') && ($second == 'integer'))) {
                return 'float';
            }
            if ((($first == 'date') && ($second == 'datetime')) ||
                (($first == 'datetime') && ($second == 'date'))) {
                    return 'datetime';
                }
                if (is_null($first) || is_null($second)) {
                    return null;
                }
                return 'mixed';
    }
        
}