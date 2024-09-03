<?php

/**
 * @file Site.php
 * A facade to the Site facade
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2024-03-04
 * Localization: none
 * Documentation: complete
 * Tests: none
 * Coverage: unknown
 */

namespace Sunhill\Facades;

use Illuminate\Support\Facades\Facade;

class Site extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'site';
    }
}
