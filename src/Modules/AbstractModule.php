<?php
/**
 * @file AbstractModule.php
 * Provides the basic class for all other modules
 * Lang en
 * Reviewstatus: 2024-04-08
 * Localization:
 * Documentation:
 * Tests:
 * Coverage: unknown
 * PSR-State: complete
 */

namespace Sunhill\Framework\Modules;

use Sunhill\Framework\Traits\NameAndDescription;
use Sunhill\Framework\Traits\Owner;

/**
 * The class defines the rudimental functions that each module should share. That is
 * naming and hirarchy
 * @author lokal
 *
 */
class AbstractModule
{

    use NameAndDescription;
    use Owner;
    
}