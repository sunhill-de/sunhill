<?php

/**
 * @file Query.php
 * A facade to the QueryManager
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2025-04-01
 * Create date: 2025-04-01
 * Localization: none
 * Documentation: complete
 * @subpackage query
 * Tests: none
 * Coverage: unknown
 */

namespace Sunhill\Facades;

use Illuminate\Support\Facades\Facade;

class Query extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'queries';
    }
}
