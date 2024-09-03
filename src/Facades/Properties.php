<?php

/**
 * @file Properties.php
 * A facade to the PropertyManager
 * @author Klaus Dimde
 * ---------------------------------------------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2024-03-04
 * Localization: none
 * Documentation: complete
 * Tests: none
 * Coverage: unknown
 */

namespace Sunhill\Properties\Facades;

use Illuminate\Support\Facades\Facade;

class Properties extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'properties';
    }
}
