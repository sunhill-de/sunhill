<?php

/**
 * @file Query.php
 * A facade to the QueryManager
 *
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2025-04-01
 * Create date: 2025-04-01
 * Localization: none
 * Documentation: complete
 */

namespace Sunhill\Facades;

use Illuminate\Support\Facades\Facade;

class Queries extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'queries';
    }
}
