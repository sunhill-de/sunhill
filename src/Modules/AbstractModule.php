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

namespace Sunhill\Modules;

use Sunhill\Traits\NameAndDescription;
use Sunhill\Traits\Owner;

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